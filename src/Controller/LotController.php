<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\EnchereUtilisateur;
use App\Repository\LotRepository;
use App\Repository\FavoriRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contrôleur d'affichage et d'enchères pour les lots.
 *
 * - Liste et détail des lots
 * - Endpoint JSON pour déposer une enchère (bid) avec verrouillage pessimiste
 */
class LotController extends AbstractController
{
    // Redirection de /lot vers /lots pour éviter une 404 sur l'URL courte
    /** Redirige l'URL courte "/lot" vers l'index des lots. */
    #[Route('/lot', name: 'app_lot_redirect', methods: ['GET'])]
    public function redirectLotIndex(): Response
    {
        return $this->redirectToRoute('app_lot_index', [], 301);
    }

    /** Liste tous les lots (triés du plus récent au plus ancien). */
    #[Route('/lots', name: 'app_lot_index')]
    public function index(LotRepository $lotRepository): Response
    {
        // Nettoyer les lots orphelins (sans événement)
        $deleted = $lotRepository->removeOrphanedLots();
        
        if ($deleted > 0) {
            $this->addFlash('info', sprintf('%d lots orphelins ont été supprimés.', $deleted));
        }
        
        // Récupérer uniquement les lots qui ont un événement d'enchères valide
        $lots = $lotRepository->findAllWithValidEvent();
        
        return $this->render('lot/index.html.twig', [
            'lots' => $lots,
        ]);
    }

    // ✅ NOUVELLE ROUTE "show"
    /** Affiche le détail d'un lot par son identifiant. */
    #[Route('/lot/{id}', name: 'app_lot_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(int $id, LotRepository $repo, FavoriRepository $favoriRepo, EntityManagerInterface $em): Response
    {
        // Utiliser une requête DQL pour s'assurer que l'événement est chargé
        $query = $em->createQuery(
            'SELECT l, e FROM App\Entity\Lot l
             LEFT JOIN l.evenementEnchere e
             WHERE l.id = :id'
        )->setParameter('id', $id);
        
        try {
            $lot = $query->getOneOrNullResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $lot = null;
        }
        
        if (!$lot) {
            throw $this->createNotFoundException('Lot introuvable.');
        }
        
        // Vérifier si l'événement d'enchères existe
        if (!$lot->getEvenementEnchere()) {
            // Supprimer le lot orphelin
            $em->remove($lot);
            $em->flush();
            
            $this->addFlash('warning', 'Ce lot n\'est plus disponible car son événement d\'enchères a été supprimé.');
            return $this->redirectToRoute('app_lot_index');
        }

        $isFavorite = false;
        if ($this->getUser()) {
            /** @var \App\Entity\User $u */
            $u = $this->getUser();
            $isFavorite = $favoriRepo->isFavorite($u, $lot);
        }

        return $this->render('lot/show.html.twig', [
            'lot' => $lot,
            'is_favorite' => $isFavorite,
        ]);
    }

    /**
     * Dépose une enchère via une requête POST et renvoie un JSON.
     *
     * - Protégé par ROLE_USER
     * - Vérifie le token CSRF
     * - Vérifie l'ouverture de l'événement associé
     * - Utilise un verrou pessimiste et une transaction pour garantir l'intégrité
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/lot/{id}/bid', name: 'app_lot_bid', methods: ['POST'])]
    public function bid(int $id, Request $request, LotRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        // Récupération du lot
        $lot = $repo->find($id);
        if (!$lot) {
            $this->addFlash('danger', 'Lot introuvable.');
            return new JsonResponse(['ok' => false, 'error' => 'Lot introuvable'], 404);
        }

        // Validation CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('bid_lot_'.$lot->getId(), $token)) {
            $this->addFlash('danger', 'Erreur de sécurité (CSRF). Veuillez réessayer.');
            return new JsonResponse(['ok' => false, 'error' => 'CSRF'], 400);
        }

        // Vérifie que l'événement d'enchères est ouvert (si défini)
        $ev = $lot->getEvenementEnchere();
        if ($ev && (!$ev->estOuvert())) {
            $this->addFlash('warning', 'Enchères non ouvertes pour ce lot.');
            return new JsonResponse(['ok' => false, 'error' => 'Enchères non ouvertes'], 400);
        }

        // Montant soumis
        $amount = (float) $request->request->get('amount', 0);

        // Verrouillage pessimiste et transaction pour éviter les courses critiques
        $em->beginTransaction();
        try {
            $em->lock($lot, LockMode::PESSIMISTIC_WRITE);

            // Règle métier du minimum requis
            $current = $lot->getPrixActuel();
            $min     = $current + $lot->getIncrementMin();

            if ($amount < $min) {
                $em->rollback();
                $this->addFlash('danger', sprintf('Montant trop bas. Minimum requis: %.2f €', $min));
                return new JsonResponse([
                    'ok' => false,
                    'error' => sprintf('Montant trop bas. Minimum requis: %.2f €', $min)
                ], 400);
            }

            $bid = new EnchereUtilisateur();
            $bid->setLot($lot);
            $bid->setUser($this->getUser());
            $bid->setMontant($amount);
            $em->persist($bid);

            $em->flush();
            $em->commit();

            $this->addFlash('success', 'Votre enchère a bien été déposée.');
            $next = $lot->getPrixActuel() + $lot->getIncrementMin();

            return new JsonResponse([
                'ok' => true,
                'newPrice' => number_format($lot->getPrixActuel(), 2, ',', ' '),
                'nextMin'  => number_format($next, 2, ',', ' ')
            ]);
        } catch (\Throwable $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }
            $this->addFlash('danger', 'Erreur serveur, veuillez réessayer.');
            return new JsonResponse(['ok' => false, 'error' => 'Erreur serveur'], 500);
        }
    }
}
