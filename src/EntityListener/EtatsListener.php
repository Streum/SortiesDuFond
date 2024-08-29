<?php

namespace App\EntityListener;

use App\Entity\Sorties;
use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;
class EtatsListener
{
    public function __construct(private readonly EtatsRepository $etatsRepository)
    {}

    public function defaultStatus(Sorties $sorties, LifecycleEventArgs $event){
        $etat = $this->etatsRepository->findOneById(1);
        $sorties->setNoEtat($etat);
    }

    /*public function updateStatus(Sorties $sorties, LifecycleEventArgs $event){
        $sorties = $this->sortiesRepository->findAll();
        $now = new \DateTime();

        foreach ($sorties as $sortie) {
            if ($now > $sortie->getDateDebut()) {
                $etat = $this->etatsRepository->findOneBySomeField();
            }
        }
    }*/

}