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

        $etatCree = $this->findOneById(1);
        $etatCloture = $this->findOneById(3);
        $etatEnCours = $this->findOneById(4);
        $etatPasse = $this->findOneById(5);
        $etatArchive = $this->findOneById(7);

        $newEtat = null;

        if ($now < $sorties->getDateDebut()) {
            $newEtat = $etatCree;
        }

        if($now > $sorties->getDateClotureInscription() && $now < $sorties->getDateDebut()){
            $sorties->setNoEtat($etatCloture);
            $newEtat = $etatCloture;
        }

        //gérer condition
        if ($now > $sorties->getDateDebut() && $now < $sorties->getDateFin()){
            $sorties->setNoEtat($etatEnCours);
            $newEtat = $etatEnCours;
        }

        if($now > $sorties->getdateFin()){
            $sorties->setNoEtat($etatPasse);
            $newEtat = $etatPasse;
        }
        // Détermine la date un mois avant aujourd'hui
        $oneMonthAgo = (new \DateTime())->modify('-1 month')->setTime(0, 0, 0);
        if($sorties->getDateDebut() < $oneMonthAgo){
            $sorties->setNoEtat($etatArchive);
            $newEtat = $etatArchive;
        }

        $this->em->flush();

        return $newEtat;
    }

    public function openStatus(Sorties $sorties){
        $etatOuvert = $this->findOneById(2);
        $sorties->setNoEtat($etatOuvert);

        $this->em->flush();

        return $etatOuvert;
    }



    //    /**
    //     * @return Etats[] Returns an array of Etats objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

        public function findOneById($value): ?Etats
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.id = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
