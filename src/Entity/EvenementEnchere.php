<?php

namespace App\Entity;

use App\Repository\EvenementEnchereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementEnchereRepository::class)]
class EvenementEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre lisible de l’événement (ex. "Vente Bijoux Anciens")
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    // Date/heure de début
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $debutAt = null;

    // Date/heure de fin
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $finAt = null;

    // Statut simple : "programmé", "ouvert", "clos"
    #[ORM\Column(length: 20)]
    private ?string $statut = 'programmé';

    // Relation inverse vers Lot (un événement possède plusieurs lots)
    #[ORM\OneToMany(mappedBy: 'evenementEnchere', targetEntity: Lot::class, orphanRemoval: true)]
    private Collection $lots;

    public function __construct()
    {
        $this->lots = new ArrayCollection();
    }

    // Getters/Setters
    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getDebutAt(): ?\DateTimeImmutable { return $this->debutAt; }
    public function setDebutAt(\DateTimeImmutable $debutAt): self { $this->debutAt = $debutAt; return $this; }

    public function getFinAt(): ?\DateTimeImmutable { return $this->finAt; }
    public function setFinAt(\DateTimeImmutable $finAt): self { $this->finAt = $finAt; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    /** @return Collection<int, Lot> */
    public function getLots(): Collection { return $this->lots; }

    public function addLot(Lot $lot): self
    {
        if (!$this->lots->contains($lot)) {
            $this->lots->add($lot);
            $lot->setEvenementEnchere($this);
        }
        return $this;
    }

    public function removeLot(Lot $lot): self
    {
        if ($this->lots->removeElement($lot)) {
            if ($lot->getEvenementEnchere() === $this) {
                $lot->setEvenementEnchere(null);
            }
        }
        return $this;
    }

    // Helper utile côté template/contrôleur
    public function estOuvert(): bool
    {
        $now = new \DateTimeImmutable();
        return $this->statut === 'ouvert' && $this->debutAt <= $now && $now < $this->finAt;
    }
}
