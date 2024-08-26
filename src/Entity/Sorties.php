<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCloture = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionInfos = null;

    #[ORM\Column(nullable: true)]
    private ?int $etatSortie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;



    /**
     * @var Collection<int, Inscriptions>
     */
    #[ORM\OneToMany(targetEntity: Inscriptions::class, mappedBy: 'id')]
    private Collection $inscriptions;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etats $noEtat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieux $noLieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $noParticipant = null;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNoSortie(int $noSortie): static
    {
        $this->noSortie = $noSortie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeInterface $dateCloture): static
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescriptionInfos(): ?string
    {
        return $this->descriptionInfos;
    }

    public function setDescriptionInfos(?string $descriptionInfos): static
    {
        $this->descriptionInfos = $descriptionInfos;

        return $this;
    }

    public function getEtatSortie(): ?int
    {
        return $this->etatSortie;
    }

    public function setEtatSortie(?int $etatSortie): static
    {
        $this->etatSortie = $etatSortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): static
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }


    /**
     * @return Collection<int, Inscriptions>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setNoSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getNoSortie() === $this) {
                $inscription->setNoSortie(null);
            }
        }

        return $this;
    }

    public function getNoEtat(): ?Etats
    {
        return $this->noEtat;
    }

    public function setNoEtat(?Etats $noEtat): static
    {
        $this->noEtat = $noEtat;

        return $this;
    }

    public function getNoLieu(): ?Lieux
    {
        return $this->noLieu;
    }

    public function setNoLieu(?Lieux $noLieu): static
    {
        $this->noLieu = $noLieu;

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
}
