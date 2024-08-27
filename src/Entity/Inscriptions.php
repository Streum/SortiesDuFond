<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Participant::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $noParticipant = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Sorties::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sorties $noSortie = null;

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

    public function getNoSortie(): ?Sorties
    {
        return $this->noSortie;
    }

    public function setNoSortie(?Sorties $noSortie): static
    {
        $this->noSortie = $noSortie;

        return $this;
    }
}
