<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VillesRepository::class)]
class Villes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $noVille = null;

    #[ORM\Column(length: 30)]
    private ?string $nomVille = null;

    #[ORM\Column(length: 10)]
    private ?string $codePostal = null;

    /**
     * @var Collection<int, Lieux>
     */
    #[ORM\OneToMany(targetEntity: Lieux::class, mappedBy: 'noVille')]
    private Collection $noLieux;

    public function __construct()
    {
        $this->noLieux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoVille(): ?int
    {
        return $this->noVille;
    }

    public function setNoVille(int $noVille): static
    {
        $this->noVille = $noVille;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(string $nomVille): static
    {
        $this->nomVille = $nomVille;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieux>
     */
    public function getNoLieux(): Collection
    {
        return $this->noLieux;
    }

    public function addNoLieux(Lieux $noLieux): static
    {
        if (!$this->noLieux->contains($noLieux)) {
            $this->noLieux->add($noLieux);
            $noLieux->setNoVille($this);
        }

        return $this;
    }

    public function removeNoLieux(Lieux $noLieux): static
    {
        if ($this->noLieux->removeElement($noLieux)) {
            // set the owning side to null (unless already changed)
            if ($noLieux->getNoVille() === $this) {
                $noLieux->setNoVille(null);
            }
        }

        return $this;
    }
}
