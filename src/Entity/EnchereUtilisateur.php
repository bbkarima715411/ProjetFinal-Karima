<?php

namespace App\Entity;

use App\Repository\EnchereUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * Représente une enchère posée par un utilisateur sur un `Lot`.
 *
 * Chaque entrée correspond à une offre. Un `Lot` peut avoir plusieurs `EnchereUtilisateur`.
 * Le champ `creeLe` est renseigné automatiquement au moment de l'instanciation.
 */
#[ORM\Entity(repositoryClass: EnchereUtilisateurRepository::class)]
#[ORM\Table(name: 'enchere_utilisateur')]
class EnchereUtilisateur
{
    /** Identifiant technique auto-incrémenté */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Montant de l'offre (en euros) */
    #[ORM\Column]
    private float $montant = 0.0;

    /** Lot concerné par l'offre (obligatoire) */
    #[ORM\ManyToOne(inversedBy: 'encheresUtilisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lot $lot = null;

    /**
     * Utilisateur ayant posé l'offre (obligatoire).
     * Note: on référence l'entité `User` (sécurité/compte), et non une entité "Utilisateur".
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /** Date/heure de création de l'offre (immutable) */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $creeLe;

    /** Initialise la date de création à "maintenant" */
    public function __construct()
    {
        // La date de création est automatiquement renseignée au moment de l'instanciation.
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
