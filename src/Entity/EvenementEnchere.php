<?php

namespace App\Entity;

use App\Repository\EvenementEnchereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementEnchereRepository::class)]
class EvenementEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $EvenementEnchere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenementEnchere(): ?float
    {
        return $this->EvenementEnchere;
    }

    public function setEvenementEnchere(?float $EvenementEnchere): static
    {
        $this->EvenementEnchere = $EvenementEnchere;

        return $this;
    }
}
