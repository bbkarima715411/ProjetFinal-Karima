<?php
namespace App\Service;

use App\Entity\{Lot, EnchereUtilisateur, User};
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service centralisant la logique métier des enchères.
 *
 * Vérifie les règles (montant minimum) et persiste les offres.
 */
class GestionEncheres
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Dépose une offre pour un couple (Lot, User) au montant donné.
     *
     * Règle appliquée: montant >= prixActuel + incrementMin.
     * @throws \RuntimeException si le montant est insuffisant.
     */
    public function deposerOffre(Lot $lot, User $user, float $montant): void
    {
        // Option A: Vérifie que l'événement d'enchères est ouvert (mode DEV 8h-20h)
        $ev = $lot->getEvenementEnchere();
        if ($ev && !$ev->estOuvert()) {
            throw new \RuntimeException('Enchères non ouvertes pour ce lot.');
        }

        // Calcule le minimum requis (prix actuel + incrément)
        $current = $lot->getPrixActuel();
        $min = $current + $lot->getIncrementMin();
        if ($montant < $min) {
            throw new \RuntimeException(sprintf('Montant trop bas. Minimum requis: %.2f €', $min));
        }

        // Crée et persiste l'enchère utilisateur
        $bid = new EnchereUtilisateur();
        $bid->setLot($lot)->setUser($user)->setMontant($montant);
        $this->em->persist($bid);
        $this->em->flush();
    }
}
