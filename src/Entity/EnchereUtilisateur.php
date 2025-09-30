<?php

namespace App\Entity;

use App\Repository\EnchereUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnchereUtilisateurRepository::class)]
class EnchereUtilisateur
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    private ?int $id = null;

    // NON NULL maintenant
    #[ORM\Column]
    private float $montant = 0.0;

    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lot $lot = null;

    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    // Nouveau : date
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $creeLe;

    public function __construct()
    {
        $this->creeLe = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }

    public function getLot(): ?Lot { return $this->lot; }
    public function setLot(?Lot $lot): self { $this->lot = $lot; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): self { $this->utilisateur = $utilisateur; return $this; }

    public function getCreeLe(): \DateTimeImmutable { return $this->creeLe; }
    public function setCreeLe(\DateTimeImmutable $d): self { $this->creeLe = $d; return $this; }
}
