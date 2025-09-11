<?php

namespace App\Entity;

use App\Repository\LotRepository;
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

    public function setFacture(?string $Facture): static
    {
        $this->Facture = $Facture;

        return $this;
    }
}
