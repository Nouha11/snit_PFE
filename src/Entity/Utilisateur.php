<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_u = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $pren = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'integer')]
    private ?int $n_bur = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_des', referencedColumnName: 'id_des')]
    private ?Direction $direction = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinTable(
        name: 'utilisateur_role',
        joinColumns: [
            new ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id_u')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Equipement::class)]
    private Collection $equipements;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getIdU(): ?int { return $this->id_u; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPren(): ?string { return $this->pren; }
    public function setPren(string $pren): static { $this->pren = $pren; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getMobile(): ?string { return $this->mobile; }
    public function setMobile(string $mobile): static { $this->mobile = $mobile; return $this; }

    public function getNbur(): ?int
    {
        return $this->n_bur;
    }

    public function setNbur(int $n_bur): self
    {
        $this->n_bur = $n_bur;
        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function getEquipements(): Collection { return $this->equipements; }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements[] = $equipement;
            $equipement->setUtilisateur($this);
        }
        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            if ($equipement->getUtilisateur() === $this) {
                $equipement->setUtilisateur(null);
            }
        }
        return $this;
    }

    
    // Role management methods
    public function getUserRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);
        return $this;
    }

    public function hasRole(string $roleLibelle): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getLibelle() === $roleLibelle) {
                return true;
            }
        }
        return false;
    }

    // PasswordAuthenticatedUserInterface method
    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // UserInterface methods
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = [];
        
        // Convert Role entities to string array
        foreach ($this->roles as $role) {
            $roles[] = 'ROLE_' . strtoupper($role->getLibelle());
        }
        
        // Guarantee every user at least has ROLE_USER
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // For backward compatibility if needed
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

}