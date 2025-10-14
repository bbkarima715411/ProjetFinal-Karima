<?php

namespace App\Entity;

use App\Repository\EvenementEnchereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un événement d'enchères (fenêtre pendant laquelle on peut enchérir sur des lots).
 *
 * Contient une collection de `Lot` et des utilitaires d'ouverture/fermeture.
 */
#[ORM\Entity(repositoryClass: EvenementEnchereRepository::class)]
class EvenementEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Titre lisible de l'événement */
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    /** Date/heure de début (immutable) */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $debutAt = null;

    /** Date/heure de fin (immutable) */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $finAt = null;

    /** Statut textuel (ex: programmé, en_cours, clos) */
    #[ORM\Column(length: 20)]
    private ?string $statut = 'programmé';

    /** Lots rattachés à l'événement */
    #[ORM\OneToMany(mappedBy: 'evenementEnchere', targetEntity: Lot::class, orphanRemoval: true)]
    private Collection $lots;

    public function __construct()
    {
        $this->lots = new ArrayCollection();
    }

    // --- 🕗 Helpers : horaires fixes 8h-20h ---
    /**
     * Renvoie le début de la journée à 8h dans le fuseau donné (Europe/Paris par défaut).
     */
    public static function debutDuJour(?\DateTimeZone $tz = null): \DateTimeImmutable
    {
        $tz ??= new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);
        return $now->setTime(8, 0, 0);
    }

    /**
     * Renvoie la fin de la journée à 20h dans le fuseau donné (Europe/Paris par défaut).
     */
    public static function finDuJour(?\DateTimeZone $tz = null): \DateTimeImmutable
    {
        $tz ??= new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);
        return $now->setTime(20, 0, 0);
    }

    /**
     * Indique si l'événement est ouvert (mode DEV: ouvert 8h-20h chaque jour).
     */
    public function estOuvert(): bool
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $maintenant = new \DateTimeImmutable('now', $tz);
        $debut = self::debutDuJour($tz);
        $fin = self::finDuJour($tz);

        return $maintenant >= $debut && $maintenant < $fin;
    }

    /**
     * Minutes restantes avant la fermeture effective (0 si dépassée). Null si `finAt` non définie.
     */
    public function minutesAvantFermeture(?\DateTimeZone $tz = null): ?int
{
    if (!$this->finAt) return null;
    $tz ??= new \DateTimeZone('Europe/Paris');
    $now = new \DateTimeImmutable('now', $tz);
    $diff = $this->finAt->getTimestamp() - $now->getTimestamp();
    return $diff < 0 ? 0 : (int) floor($diff / 60);
}

    /**
     * True si l'événement ferme dans moins d'une heure et n'est pas déjà fermé.
     */
    public function fermeDansMoinsDuneHeure(?\DateTimeZone $tz = null): bool
{
    if (!$this->finAt) return false;
    $tz ??= new \DateTimeZone('Europe/Paris');
    $now = new \DateTimeImmutable('now', $tz);
    $diff = $this->finAt->getTimestamp() - $now->getTimestamp();
    return $diff > 0 && $diff <= 3600;
}


    // --- Getters / Setters ---
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
}
