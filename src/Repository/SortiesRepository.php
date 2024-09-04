<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sorties>
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    public function findFilteredSorties($data, $user)
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->join('s.noEtat', 'e');

        $statesToInclude = [2, 3, 4];
        if (empty($data['passee'])) {
            $queryBuilder->andWhere('e.id IN (:states)')
                ->setParameter('states', $statesToInclude);
        } else {
            $statesToInclude[] = 5; // Ajout des sorties passées
            $queryBuilder->andWhere('e.id IN (:states)')
                ->setParameter('states', $statesToInclude);
            $queryBuilder->orWhere('s.dateFin < :now')
                ->setParameter('now', new \DateTime());
        }

        $queryBuilder->andWhere('e.id IN (:states)')
            ->setParameter('states', $statesToInclude);

        if (!empty($data['noLieu'])) {
            $queryBuilder->join('s.noLieu', 'l')
                ->join('l.noVille', 'si')
                ->andWhere('si.id = :noLieu')
                ->setParameter('noLieu', $data['noLieu']);
        }

        if (!empty($data['nom'])) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $data['nom'] . '%');
        }

        if (!empty($data['dateDebut'])) {
            $queryBuilder->andWhere('s.dateDebut >= :dateDebut OR s.dateFin >= :dateDebut')
                ->setParameter('dateDebut', $data['dateDebut']);
        }

        if (!empty($data['dateFin'])) {
            $queryBuilder->andWhere('s.dateDebut <= :dateFin OR s.dateFin <= :dateFin')
                ->setParameter('dateFin', $data['dateFin']);
        }

        if (!empty($data['orga'])) {
            $queryBuilder->join('s.noParticipant', 'p')
                ->andWhere('p.id = :organisateur')
                ->setParameter('organisateur', $user);
        }

        if (!empty($data['passee'])) {
            // Inclure les sorties passées dans les résultats
            $queryBuilder->andWhere('s.dateFin < :now')
                ->setParameter('now', new \DateTime());
        }

        if (!empty($data['isInscrit'])) {
            $queryBuilder->join('s.inscriptions', 'i')
                ->andWhere('i.noParticipant = :participant')
                ->setParameter('participant', $user);
        }

        if (!empty($data['isNotInscrit'])) {
            $queryBuilder->leftJoin('s.inscriptions', 'i_not')
                ->andWhere('i_not.noParticipant IS NULL OR i_not.noParticipant != :participant')
                ->setParameter('participant', $user);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findOneBySomeField($value): ?Sorties
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function calculateDuration($dateDebut, $dateFin)
    {
        $duree = date_diff($dateDebut, $dateFin);

        $minutes = $duree->days * 24 * 60;
        $minutes += $duree->h * 60;
        $minutes += $duree->i;

        return $minutes;
    }



    public function findPaginatedSorties(int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query, true);
    }

}
