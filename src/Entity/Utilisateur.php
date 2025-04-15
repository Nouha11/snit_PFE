<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
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

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_des', referencedColumnName: 'id_des')]
    private ?Designation $designation = null;


    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Equipement::class)]
    private Collection $equipements;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
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

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getDesignation(): ?Designation
    {
        return $this->designation;
    }

    public function setDesignation(?Designation $designation): self
    {
        $this->designation = $designation;
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

}
