<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreationSortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('+30 minutes', message: 'La date spécifiée n\'est pas valide.')]
    private ?\DateTimeInterface $dateDebut = null;

    private ?\DateTimeInterface $dateFinSortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\GreaterThan('now', message: 'La date spécifiée n\'est pas valide.')]
    #[Assert\LessThan(propertyPath: "dateDebut", message: 'La date limite d\'inscription ne peut pas être postérieure au début de la sortie.')]
    private ?\DateTimeInterface $dateClotureInscription = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(30, message: "La sortie doit faire au moins 30 minutes.")]
    private ?int $duree = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de participants doit être supérieur à 0.')]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionInfos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    /**
     * @var Collection<int, Inscriptions>
     */
    #[ORM\OneToMany(mappedBy: 'noSortie', targetEntity: Inscriptions::class)]
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreationSortie;
    }

    #[ORM\PrePersist]
    public function setDateCreation(): static
    {
        $this->dateCreationSortie = new \DateTime();

        return $this;
    }

    public function getDateClotureInscription(): ?\DateTimeInterface
    {
        return $this->dateClotureInscription;
    }

    public function setDateClotureInscription(\DateTimeInterface $dateClotureInscription): static
    {
        $this->dateClotureInscription = $dateClotureInscription;

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

    public function getDateFin(): ?\DateTimeInterface
    {
        if ($this->dateDebut === null || $this->duree === null) {
            return null;
        }

        $dateFin = (clone $this->dateDebut)->modify('+' . $this->duree . ' minutes');
        return $dateFin;
    }


    // Nouvelle méthode pour vérifier les conditions d'inscription
    public function peutSInscrire(): bool
    {
        // Vérifie si la date de clôture est dépassée
        if ($this->dateClotureInscription <= new \DateTime()) {
            return false;
        }

        // Vérifie si le nombre maximum de participants est atteint
        if ($this->inscriptions->count() >= $this->nbInscriptionsMax) {
            return false;
        }

        return true;
    }

}
