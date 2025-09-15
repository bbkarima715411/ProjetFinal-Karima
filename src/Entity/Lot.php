<?php

namespace App\Entity;

use App\Repository\LotRepository;
use App\Entity\EvenementEnchere;
use App\Entity\EnchereUtilisateur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotRepository::class)]
class Lot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Lot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Categorie = null;

    #[ORM\Column(nullable: true)]
    private ?float $Paiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Facture = null;

    #[ORM\ManyToOne(inversedBy: 'lots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EvenementEnchere $evenementEnchere = null;

    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: EnchereUtilisateur::class, orphanRemoval: true)]
    private Collection $encheresUtilisateur;
    
    public function __construct()
    {
        $this->encheresUtilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLot(): ?string
    {
        return $this->Lot;
    }

    public function setLot(?string $Lot): static
    {
        $this->Lot = $Lot;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(?string $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getPaiement(): ?float
    {
        return $this->Paiement;
    }

    public function setPaiement(?float $Paiement): static
    {
        $this->Paiement = $Paiement;

        return $this;
    }

    public function getFacture(): ?string
    {
        return $this->Facture;
    }

    public function getEvenementEnchere(): ?EvenementEnchere
    {
        return $this->evenementEnchere;
    }

    public function setEvenementEnchere(?EvenementEnchere $evenementEnchere): self
    {
        $this->evenementEnchere = $evenementEnchere;
        return $this;
    }

    /**
     * @return Collection<int, EnchereUtilisateur>
     */
    public function getEncheresUtilisateur(): Collection
    {
        return $this->encheresUtilisateur;
    }

    public function addEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self
    {
        if (!$this->encheresUtilisateur->contains($encheresUtilisateur)) {
            $this->encheresUtilisateur[] = $encheresUtilisateur;
            $encheresUtilisateur->setLot($this);
        }

        return $this;
    }

    public function removeEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self
    {
        if ($this->encheresUtilisateur->removeElement($encheresUtilisateur)) {
            // set the owning side to null (unless already changed)
            if ($encheresUtilisateur->getLot() === $this) {
                $encheresUtilisateur->setLot(null);
            }
        }

        return $this;
    }

    public function setFacture(?string $Facture): static
    {
        $this->Facture = $Facture;

        return $this;
    }
}
