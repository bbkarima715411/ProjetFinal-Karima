<?php
namespace App\Service;

use App\Entity\{Lot, EnchereUtilisateur, User};
use Doctrine\ORM\EntityManagerInterface;

class GestionEncheres
{
    public function __construct(private EntityManagerInterface $em) {}

    public function deposerOffre(Lot $lot, User $user, float $montant): void
    {
        $current = $lot->getPrixActuel();
        $min = $current + $lot->getIncrementMin();
        if ($montant < $min) {
            throw new \RuntimeException(sprintf('Montant trop bas. Minimum requis: %.2f €', $min));
        }

        $bid = new EnchereUtilisateur();
        $bid->setLot($lot)->setUser($user)->setMontant($montant);
        $this->em->persist($bid);
        $this->em->flush();
    }
}
