<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeIntervention = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateIntervention = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(name: "equipement_id", referencedColumnName: "id_eq", nullable: true)]
    private ?Equipement $equipement= null;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(name: "contrat_id", referencedColumnName: "id", nullable: true)]
    private ?Contrat $contrat = null;

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }
    
    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }
    
    public function setEquipement(?Equipement $equipement): self
    {
        $this->equipement = $equipement;
    
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeIntervention(): ?string
    {
        return $this->typeIntervention;
    }

    public function setTypeIntervention(string $typeIntervention): static
    {
        $this->typeIntervention = $typeIntervention;

        return $this;
    }

    public function getDateIntervention(): ?\DateTimeInterface
    {
        return $this->dateIntervention;
    }

    public function setDateIntervention(\DateTimeInterface $dateIntervention): static
    {
        $this->dateIntervention = $dateIntervention;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
