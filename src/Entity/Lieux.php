<?php

namespace App\Entity;

use App\Repository\LieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuxRepository::class)]
class Lieux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nomLieu = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $rue = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Vous devez entrer une valeur entre {{ min }} et {{ max }}')]
    private ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Vous devez entrer une valeur entre {{ min }} et {{ max }}')]
    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $noVille = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNomLieu(): ?string
    {
        return $this->nomLieu;
    }

    public function setNomLieu(string $nomLieu): static
    {
        $this->nomLieu = $nomLieu;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): static
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getNoVille(): ?Villes
    {
        return $this->noVille;
    }

    public function setNoVille(?Villes $noVille): static
    {
        $this->noVille = $noVille;

        return $this;
    }

}
