<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numContrat = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnregistrement = null;

    #[ORM\Column(length: 255)]
    private ?string $numEnregistrement = null;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: Equipement::class, cascade: ['persist', 'remove'])]
    private Collection $equipements;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: Intervention::class, cascade: ['persist', 'remove'])]
    private Collection $interventions;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->interventions = new ArrayCollection();

    }
    public function __toString(): string
    {
        return $this->numContrat ?? '';
    }

    
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions[] = $intervention;
            $intervention->setContrat($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->removeElement($intervention)) {
            // Set the owning side to null (unless already changed)
            if ($intervention->getContrat() === $this) {
                $intervention->setContrat(null);
            }
        }

        return $this;
    }

    public function getEquipements(): Collection 
    { return $this->equipements; }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements[] = $equipement;
            $equipement->setContrat($this);

        }
        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            if ($equipement->getContrat() === $this) {
                $equipement->setContrat(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumContrat(): ?string
    {
        return $this->numContrat;
    }

    public function setNumContrat(string $numContrat): static
    {
        $this->numContrat = $numContrat;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    public function getNumEnregistrement(): ?string
    {
        return $this->numEnregistrement;
    }

    public function setNumEnregistrement(string $numEnregistrement): static
    {
        $this->numEnregistrement = $numEnregistrement;

        return $this;
    }
}
