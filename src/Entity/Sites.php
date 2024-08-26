<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SitesRepository::class)]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $noSite = null;

    #[ORM\Column(length: 30)]
    private ?string $nomSite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoSite(): ?int
    {
        return $this->noSite;
    }

    public function setNoSite(int $noSite): static
    {
        $this->noSite = $noSite;

        return $this;
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
}
