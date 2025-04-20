<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_eq = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $designation = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $Modele = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $Inventaire = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $N_serie = null;

    #[ORM\Column(type: 'text')]
    private ?string $Description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $Etat = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'equipements')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id_u", nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Consommable::class, inversedBy: 'equipements')]
    #[ORM\JoinColumn(name: "consommable_id", referencedColumnName: "id_cons", nullable: true)]
    private ?Consommable $consommable = null;
    

    public function getUtilisateur(): ?Utilisateur 
    { return $this->utilisateur; }
    
    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getConsommable(): ?Consommable
    {
        return $this->consommable;
    }

    public function setConsommable(?Consommable $consommable): static
    {
        $this->consommable = $consommable;
        return $this;
    }

    public function getId_eq(): ?int
    {
        return $this->id_eq;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;
        return $this;
    }    

    public function getModele(): ?string
    {
        return $this->Modele;
    }

    public function setModele(string $Modele): self
    {
        $this->Modele = $Modele;
        return $this;
    }

    public function getInventaire(): ?string
    {
        return $this->Inventaire;
    }

    public function setInventaire(string $Inventaire): self
    {
        $this->Inventaire = $Inventaire;
        return $this;
    }

    public function getNserie(): ?string
    {
        return $this->N_serie;
    }

    public function setNserie(string $N_serie): self
    {
        $this->N_serie = $N_serie;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(string $Etat): self
    {
        $this->Etat = $Etat;
        return $this;
    }
}
