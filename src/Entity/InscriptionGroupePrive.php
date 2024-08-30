<?php

namespace App\Entity;

use App\Repository\InscriptionGroupePriveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionGroupePriveRepository::class)]
class InscriptionGroupePrive
{


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inscriptionGroupePrives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $noParticipant = null;
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupePrive $noGroupe = null;




    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getNoParticipant(): ?Participant
    {
        return $this->noParticipant;
    }

    public function setNoParticipant(?Participant $noParticipant): static
    {
        $this->noParticipant = $noParticipant;

        return $this;
    }

    public function getNoGroupe(): ?GroupePrive
    {
        return $this->noGroupe;
    }

    public function setNoGroupe(?GroupePrive $noGroupe): static
    {
        $this->noGroupe = $noGroupe;

        return $this;
    }


}
