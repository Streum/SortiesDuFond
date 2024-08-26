<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $noParticipant;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sorties $noSortie = null;

    public function __construct()
    {
        $this->noParticipant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getNoParticipant(): Collection
    {
        return $this->noParticipant;
    }

    public function addNoParticipant(Participant $noParticipant): static
    {
        if (!$this->noParticipant->contains($noParticipant)) {
            $this->noParticipant->add($noParticipant);
        }

        return $this;
    }

    public function removeNoParticipant(Participant $noParticipant): static
    {
        $this->noParticipant->removeElement($noParticipant);

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
