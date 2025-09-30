<?php

namespace App\Entity;

use App\Repository\EvenementEnchereRepository;
use App\Entity\Lot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementEnchereRepository::class)]
class EvenementEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $EvenementEnchere = null;

    #[ORM\OneToMany(mappedBy: 'evenementEnchere', targetEntity: Lot::class, orphanRemoval: true)]
    private Collection $lots;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenementEnchere(): ?float
    {
        return $this->EvenementEnchere;
    }

    /**
     * @return Collection<int, Lot>
     */
    public function getLots(): Collection
    {
        return $this->lots;
    }

    public function addLot(Lot $lot): self
    {
        if (!$this->lots->contains($lot)) {
            $this->lots[] = $lot;
            $lot->setEvenementEnchere($this);
        }

        return $this;
    }

    public function removeLot(Lot $lot): self
    {
        if ($this->lots->removeElement($lot)) {
            // set the owning side to null (unless already changed)
            if ($lot->getEvenementEnchere() === $this) {
                $lot->setEvenementEnchere(null);
            }
        }

        return $this;
    }

    public function setEvenementEnchere(?float $EvenementEnchere): static
    {
        $this->EvenementEnchere = $EvenementEnchere;

        return $this;
    }
}
