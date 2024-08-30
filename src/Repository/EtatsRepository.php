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
        $etatEnCours = $this->findOneById(4);
        $etatPasse = $this->findOneById(5);

        $newEtat = null;

        if ($now < $sorties->getDateDebut()) {
            $newEtat = $etatCree;
        }

        if ($now > $sorties->getDateDebut()){
            $sorties->setNoEtat($etatEnCours);
            $newEtat = $etatEnCours;
        }

        if($now > $sorties->getDateClotureInscription()){
            $sorties->setNoEtat($etatPasse);
            $newEtat = $etatPasse;
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
