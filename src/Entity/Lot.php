<?php

namespace App\Entity;

use App\Repository\LotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotRepository::class)]
class Lot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // === NOUVEAUX CHAMPS utiles pour tes pages ===
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column(type: 'boolean')]
    private bool $estVendu = false;

    // === TES CHAMPS EXISTANTS (on les garde) ===
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

    // Relations
    #[ORM\ManyToOne(inversedBy: 'lots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EvenementEnchere $evenementEnchere = null;

    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: EnchereUtilisateur::class, orphanRemoval: true)]
    private Collection $encheresUtilisateur;

    // Champs d’enchères
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

    // === GETTERS/SETTERS ===

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string
    {
        // fallback si tu avais rempli l’ancien champ "Lot"
        return $this->titre ?? $this->Lot;
    }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): self { $this->description = $d; return $this; }

    public function getDateFin(): ?\DateTimeImmutable { return $this->dateFin; }
    public function setDateFin(?\DateTimeImmutable $d): self { $this->dateFin = $d; return $this; }

    public function isEstVendu(): bool { return $this->estVendu; }
    public function setEstVendu(bool $v): self { $this->estVendu = $v; return $this; }

    public function getLot(): ?string { return $this->Lot; }
    public function setLot(?string $Lot): self { $this->Lot = $Lot; return $this; }

    public function getCategorie(): ?string { return $this->Categorie; }
    public function setCategorie(?string $Categorie): self { $this->Categorie = $Categorie; return $this; }

    public function getPaiement(): ?float { return $this->Paiement; }
    public function setPaiement(?float $Paiement): self { $this->Paiement = $Paiement; return $this; }

    public function getFacture(): ?string { return $this->Facture; }
    public function setFacture(?string $Facture): self { $this->Facture = $Facture; return $this; }

    public function getImageFilename(): ?string { return $this->imageFilename; }
    public function setImageFilename(?string $imageFilename): self { $this->imageFilename = $imageFilename; return $this; }

    public function getEvenementEnchere(): ?EvenementEnchere { return $this->evenementEnchere; }
    public function setEvenementEnchere(?EvenementEnchere $evenementEnchere): self { $this->evenementEnchere = $evenementEnchere; return $this; }

    /** @return Collection<int, EnchereUtilisateur> */
    public function getEncheresUtilisateur(): Collection { return $this->encheresUtilisateur; }

    public function addEncheresUtilisateur(EnchereUtilisateur $enchere): self
    {
        if (!$this->encheresUtilisateur->contains($enchere)) {
            $this->encheresUtilisateur[] = $enchere;
            $enchere->setLot($this);
        }
        return $this;
    }

    public function removeEncheresUtilisateur(EnchereUtilisateur $enchere): self
    {
        if ($this->encheresUtilisateur->removeElement($enchere)) {
            if ($enchere->getLot() === $this) {
                $enchere->setLot(null);
            }
        }
        return $this;
    }

    public function getPrixDepart(): float { return $this->prixDepart; }
    public function setPrixDepart(float $p): self { $this->prixDepart = $p; return $this; }

    public function getIncrementMin(): float { return $this->incrementMin; }
    public function setIncrementMin(float $i): self { $this->incrementMin = $i; return $this; }

    public function getGagnant(): ?User { return $this->gagnant; }
    public function setGagnant(?User $u): self { $this->gagnant = $u; return $this; }

    // Prix actuel = max(offres) ou prix de départ
    public function getPrixActuel(): float
    {
        $max = $this->prixDepart;
        foreach ($this->encheresUtilisateur as $e) {
            $m = (float)($e->getMontant() ?? 0);
            if ($m > $max) $max = $m;
        }
        return $max;
    }
}
