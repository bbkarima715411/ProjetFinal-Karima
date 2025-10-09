<?php

namespace App\Service;

use App\Entity\Lot;
use App\Entity\EnchereUtilisateur;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GestionEncheres
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Dépose une offre si l'événement est ouvert et si le montant respecte l'incrément.
     * @throws \RuntimeException
     */
    public function deposerOffre(Lot $lot, User $user, float $montant): EnchereUtilisateur
    {
        $event = $lot->getEvenementEnchere();
        if (!$event || !$event->estOuvert()) {
            throw new \RuntimeException("L'événement n'est pas ouvert.");
        }

        $minimum = $lot->getPrixActuel() + $lot->getIncrementMin();
        if ($montant < $minimum) {
            throw new \RuntimeException(sprintf("Offre trop basse. Minimum requis : %.2f €", $minimum));
        }

        $offre = (new EnchereUtilisateur())
            ->setLot($lot)
            ->setUser($user)
            ->setMontant($montant);

        $this->em->persist($offre);
        $this->em->flush();

        return $offre;
    }
}
