<?php

namespace App\Entity;

use App\Repository\EnchereUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: EnchereUtilisateurRepository::class)]
#[ORM\Table(name: 'enchere_utilisateur')]
class EnchereUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private float $montant = 0.0;

    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lot $lot = null;

    // 👉 on référence bien User (et pas Utilisateur)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getCreeLe(): \DateTimeImmutable { return $this->creeLe; }
    public function setCreeLe(\DateTimeImmutable $d): self { $this->creeLe = $d; return $this; }
}
