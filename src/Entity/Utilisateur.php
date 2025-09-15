<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use App\Entity\EnchereUtilisateur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: EnchereUtilisateur::class, orphanRemoval: true)]
    private Collection $encheresUtilisateur;

    public function __construct()
    {
        $this->encheresUtilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?string
    {
        return $this->Utilisateur;
    }

    /**
     * @return Collection<int, EnchereUtilisateur>
     */
    public function getEncheresUtilisateur(): Collection
    {
        return $this->encheresUtilisateur;
    }

    public function addEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self
    {
        if (!$this->encheresUtilisateur->contains($encheresUtilisateur)) {
            $this->encheresUtilisateur[] = $encheresUtilisateur;
            $encheresUtilisateur->setUtilisateur($this);
        }

        return $this;
    }

    public function removeEncheresUtilisateur(EnchereUtilisateur $encheresUtilisateur): self
    {
        if ($this->encheresUtilisateur->removeElement($encheresUtilisateur)) {
            // set the owning side to null (unless already changed)
            if ($encheresUtilisateur->getUtilisateur() === $this) {
                $encheresUtilisateur->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function setUtilisateur(?string $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }
}
