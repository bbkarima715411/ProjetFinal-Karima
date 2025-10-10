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

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $debutAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $finAt = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'programmé';

    #[ORM\OneToMany(mappedBy: 'evenementEnchere', targetEntity: Lot::class, orphanRemoval: true)]
    private Collection $lots;

    public function __construct()
    {
        $this->lots = new ArrayCollection();
    }

    // --- 🕗 Helpers : horaires fixes 8h-20h ---
    public static function debutDuJour(?\DateTimeZone $tz = null): \DateTimeImmutable
    {
        $tz ??= new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);
        return $now->setTime(8, 0, 0);
    }

    public static function finDuJour(?\DateTimeZone $tz = null): \DateTimeImmutable
    {
        $tz ??= new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);
        return $now->setTime(20, 0, 0);
    }

    /**
     * Mode DEV : ouvert tous les jours de 8h à 20h
     */
    public function estOuvert(): bool
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $maintenant = new \DateTimeImmutable('now', $tz);
        $debut = self::debutDuJour($tz);
        $fin = self::finDuJour($tz);

        return $maintenant >= $debut && $maintenant < $fin;
    }

    public function minutesAvantFermeture(?\DateTimeZone $tz = null): ?int
{
    if (!$this->finAt) return null;
    $tz ??= new \DateTimeZone('Europe/Paris');
    $now = new \DateTimeImmutable('now', $tz);
    $diff = $this->finAt->getTimestamp() - $now->getTimestamp();
    return $diff < 0 ? 0 : (int) floor($diff / 60);
}

/** true si l'événement ferme dans < 60 min, et est encore ouvert */
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
