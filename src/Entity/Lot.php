<?php

namespace App\Entity;

use App\Repository\LotRepository;
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

    // Tes champs existants
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Lot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Categorie = null;

    #[ORM\Column(nullable: true)]
    private ?float $Paiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Facture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;

    // Relation existante
    #[ORM\ManyToOne(inversedBy: 'lots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EvenementEnchere $evenementEnchere = null;

    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: EnchereUtilisateur::class, orphanRemoval: true)]
    private Collection $encheresUtilisateur;

    // Ajouts pour l’enchère
    #[ORM\Column(type: 'float')]
    private float $prixDepart = 0.0;

    #[ORM\Column(type: 'float')]
    private float $incrementMin = 1.0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $gagnant = null;

    public function __construct()
    {
        $this->encheresUtilisateur = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLot(): ?string { return $this->Lot; }
    public function setLot(?string $Lot): static { $this->Lot = $Lot; return $this; }

    public function getCategorie(): ?string { return $this->Categorie; }
    public function setCategorie(?string $Categorie): static { $this->Categorie = $Categorie; return $this; }

    public function getPaiement(): ?float { return $this->Paiement; }
    public function setPaiement(?float $Paiement): static { $this->Paiement = $Paiement; return $this; }

    public function getFacture(): ?string { return $this->Facture; }
    public function setFacture(?string $Facture): static { $this->Facture = $Facture; return $this; }

    public function getImageFilename(): ?string { return $this->imageFilename; }
    public function setImageFilename(?string $imageFilename): static { $this->imageFilename = $imageFilename; return $this; }

    public function getEvenementEnchere(): ?EvenementEnchere { return $this->evenementEnchere; }
    public function setEvenementEnchere(?EvenementEnchere $evenementEnchere): self { $this->evenementEnchere = $evenementEnchere; return $this; }

    /** @return Collection<int, EnchereUtilisateur> */
    public function getEncheresUtilisateur(): Collection { return $this->encheresUtilisateur; }

    public function addEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self {
        if (!$this->encheresUtilisateur->contains($encheresUtilisateur)) {
            $this->encheresUtilisateur[] = $encheresUtilisateur;
            $encheresUtilisateur->setLot($this);
        }
        return $this;
    }

    public function removeEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self {
        if ($this->encheresUtilisateur->removeElement($encheresUtilisateur)) {
            if ($encheresUtilisateur->getLot() === $this) {
                $encheresUtilisateur->setLot(null);
            }
        }
        return $this;
    }

    // Nouveaux champs enchère
    public function getPrixDepart(): float { return $this->prixDepart; }
    public function setPrixDepart(float $p): self { $this->prixDepart = $p; return $this; }

    public function getIncrementMin(): float { return $this->incrementMin; }
    public function setIncrementMin(float $i): self { $this->incrementMin = $i; return $this; }

    public function getGagnant(): ?User { return $this->gagnant; }
    public function setGagnant(?User $u): self { $this->gagnant = $u; return $this; }

    // Helper : prix actuel = max(offres) ou prix de départ
    public function getPrixActuel(): float {
        $max = $this->prixDepart;
        foreach ($this->encheresUtilisateur as $e) {
            $m = (float)($e->getMontant() ?? 0);
            if ($m > $max) $max = $m;
        }
        return $max;
    }
}
