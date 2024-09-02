<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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


    //    /**
    //     * @return Sorties[] Returns an array of Sorties objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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


    public function findSortiesOneMonthOld(): array
    {
        // Détermine la date un mois avant aujourd'hui
        $oneMonthAgo = (new \DateTime())->modify('-1 month')->setTime(0, 0, 0);

        // Crée une requête pour récupérer les sorties non archivés
        return $this->createQueryBuilder('s')
            ->where('s.dateDebut > :startDate')
            ->setParameter('startDate', $oneMonthAgo)
            ->getQuery()
            ->getResult();
    }

}
