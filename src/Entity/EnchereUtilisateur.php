<?php

namespace App\Entity;

use App\Repository\EnchereUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnchereUtilisateurRepository::class)]
class EnchereUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Montant de l'enchère
    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lot $lot = null;

    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int { return $this->id; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(?float $montant): self { $this->montant = $montant; return $this; }

    public function getLot(): ?Lot { return $this->lot; }
    public function setLot(?Lot $lot): self { $this->lot = $lot; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): self { $this->utilisateur = $utilisateur; return $this; }
}
