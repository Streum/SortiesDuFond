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

    public function updateEtats(Sorties $sorties){
        $now = new \DateTime();

        $etatCree = $this->findOneById(1);
        $etatOuvert = $this->findOneById(2);
        $etatCloture = $this->findOneById(3);
        $etatEnCours = $this->findOneById(4);
        $etatPasse = $this->findOneById(5);
        $etatAnnuler = $this->findOneById(6);

        $newEtat = null;

        if ($now < $sorties->getDateDebut()) {
            $newEtat = $etatCree;
        }

        if($now > $sorties->getDateClotureInscription() && $now < $sorties->getDateDebut()){
            $sorties->setNoEtat($etatPasse);
            $newEtat = $etatCloture;
        }

        //gérer condition
        if ($now > $sorties->getDateDebut() && $now < $sorties->getDateFin()){
            $sorties->setNoEtat($etatEnCours);
            $newEtat = $etatEnCours;
        }

        $this->em->flush();

        return $newEtat;
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
