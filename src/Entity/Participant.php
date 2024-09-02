<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $administrateur = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $actif = true;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Inscriptions>
     */
    #[ORM\OneToMany(mappedBy: 'noParticipant', targetEntity: Inscriptions::class)]
    private Collection $inscriptions;

    /**
     * @var Collection<int, Sorties>
     */
    #[ORM\OneToMany(mappedBy: 'noParticipant', targetEntity: Sorties::class, orphanRemoval: true)]
    private Collection $sorties;

    #[ORM\ManyToOne(inversedBy: 'noParticipant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sites $site = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, InscriptionGroupePrive>
     */
    #[ORM\OneToMany(targetEntity: InscriptionGroupePrive::class, mappedBy: 'noParticipant')]
    private Collection $inscriptionGroupePrives;

    /**
     * @var Collection<int, GroupePrive>
     */
    #[ORM\OneToMany(targetEntity: GroupePrive::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $groupePrives;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->inscriptionGroupePrives = new ArrayCollection();
        $this->groupePrives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }

    public function isAdministrateur(): bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): void
    {
        $this->administrateur = $administrateur;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $inscription->setNoParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getNoParticipant() === $this) {
                $inscription->setNoParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sorties>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sorties $sortie): static
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
            $sortie->setNoParticipant($this);
        }

        return $this;
    }

    public function removeSortie(Sorties $sortie): static
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getNoParticipant() === $this) {
                $sortie->setNoParticipant(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Sites
    {
        return $this->site;
    }

    public function setSite(?Sites $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, InscriptionGroupePrive>
     */
    public function getInscriptionGroupePrives(): Collection
    {
        return $this->inscriptionGroupePrives;
    }

    public function addInscriptionGroupePrife(InscriptionGroupePrive $inscriptionGroupePrife): static
    {
        if (!$this->inscriptionGroupePrives->contains($inscriptionGroupePrife)) {
            $this->inscriptionGroupePrives->add($inscriptionGroupePrife);
            $inscriptionGroupePrife->setNoParticipant($this);
        }

        return $this;
    }

    public function removeInscriptionGroupePrife(InscriptionGroupePrive $inscriptionGroupePrife): static
    {
        if ($this->inscriptionGroupePrives->removeElement($inscriptionGroupePrife)) {
            // set the owning side to null (unless already changed)
            if ($inscriptionGroupePrife->getNoParticipant() === $this) {
                $inscriptionGroupePrife->setNoParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getGroupePrives(): Collection
    {
        return $this->groupePrives;
    }

    public function addGroupePrife(GroupePrive $groupePrife): static
    {
        if (!$this->groupePrives->contains($groupePrife)) {
            $this->groupePrives->add($groupePrife);
            $groupePrife->setOwner($this);
        }

        return $this;
    }

    public function removeGroupePrife(GroupePrive $groupePrife): static
    {
        if ($this->groupePrives->removeElement($groupePrife)) {
            // set the owning side to null (unless already changed)
            if ($groupePrife->getOwner() === $this) {
                $groupePrife->setOwner(null);
            }
        }

        return $this;
    }
}
