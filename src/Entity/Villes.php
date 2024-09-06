<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VillesRepository::class)]
class Villes
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÖØ-öø-ÿ\s'\-]+$/", message: "La ville ne peut contenir que des lettres, des espaces, des apostrophes ou des tirets.")]
    private ?string $nomVille = null;

    #[ORM\Column(length: 10)]
    #[Assert\Regex(pattern: "/^\d{5}$/", message: "Le code postal doit être composé de 5 chiffres.")]
    private ?string $codePostal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $urlPhotos = null;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrlPhotos(): ?string
    {
        return $this->urlPhotos;
    }

    public function setUrlPhotos(?string $urlPhotos): static
    {
        $this->urlPhotos = $urlPhotos;

        return $this;
    }


}
