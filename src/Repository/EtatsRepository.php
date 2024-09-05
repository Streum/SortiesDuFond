<?php

namespace App\Repository;

use App\Entity\Etats;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etats>
 */
class EtatsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Etats::class);
        $this->em = $em;
    }

    public function updateEtats(Sorties $sorties): ?Etats
    {
        $now = new \DateTime();
        $etats = [
            1 => $this->findOneById(1),
            3 => $this->findOneById(3),
            4 => $this->findOneById(4),
            5 => $this->findOneById(5),
            7 => $this->findOneById(7),
        ];

        $newEtat = null;

        if ($now < $sorties->getDateDebut()) {
            $newEtat = $etats[1];
        } elseif ($now > $sorties->getDateClotureInscription() && $now < $sorties->getDateDebut()) {
            $sorties->setNoEtat($etats[3]);
            $newEtat = $etats[3];
        } elseif ($now > $sorties->getDateDebut() && $now < $sorties->getDateFin()) {
            $sorties->setNoEtat($etats[4]);
            $newEtat = $etats[4];
        } elseif ($now > $sorties->getDateFin()) {
            $sorties->setNoEtat($etats[5]);
            $newEtat = $etats[5];
        }

        $oneMonthAgo = (new \DateTime())->modify('-1 month')->setTime(0, 0, 0);
        if ($sorties->getDateDebut() < $oneMonthAgo) {
            $sorties->setNoEtat($etats[7]);
            $newEtat = $etats[7];
        }

        $this->em->flush();

        return $newEtat;
    }

    public function openStatus(Sorties $sorties)
    {
        $etatOuvert = $this->findOneById(2);
        $sorties->setNoEtat($etatOuvert);

        $this->em->flush();

        return $etatOuvert;
    }

    public function findOneById($value): ?Etats
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
