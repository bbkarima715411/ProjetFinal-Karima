<?php

namespace App\Entity;

use App\Repository\EnchèreUtulisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnchèreUtulisateurRepository::class)]
class EnchèreUtulisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $EnchereUtlisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnchereUtlisateur(): ?float
    {
        return $this->EnchereUtlisateur;
    }

    public function setEnchereUtlisateur(?float $EnchereUtlisateur): static
    {
        $this->EnchereUtlisateur = $EnchereUtlisateur;

        return $this;
    }
}
