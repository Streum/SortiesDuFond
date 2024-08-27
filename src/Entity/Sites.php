<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SitesRepository::class)]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 30)]
    private ?string $nomSite = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\OneToMany(targetEntity: Participant::class, mappedBy: 'site')]
    private Collection $noParticipant;

    public function __construct()
    {
        $this->noParticipant = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }

    public function setNomSite(string $nomSite): static
    {
        $this->nomSite = $nomSite;

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
            $noParticipant->setSite($this);
        }

        return $this;
    }

    public function removeNoParticipant(Participant $noParticipant): static
    {
        if ($this->noParticipant->removeElement($noParticipant)) {
            // set the owning side to null (unless already changed)
            if ($noParticipant->getSite() === $this) {
                $noParticipant->setSite(null);
            }
        }

        return $this;
    }
}
