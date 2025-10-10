<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\EnchereUtilisateur;
use App\Repository\LotRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class LotController extends AbstractController
{
    // Redirection de /lot vers /lots pour éviter une 404 sur l'URL courte
    #[Route('/lot', name: 'app_lot_redirect', methods: ['GET'])]
    public function redirectLotIndex(): Response
    {
        return $this->redirectToRoute('app_lot_index', [], 301);
    }

    #[Route('/lots', name: 'app_lot_index')]
    public function index(LotRepository $repo): Response
    {
        $lots = $repo->findBy([], ['id' => 'DESC']);
        return $this->render('lot/index.html.twig', [
            'lots' => $lots,
        ]);
    }

    // ✅ NOUVELLE ROUTE "show"
    #[Route('/lot/{id}', name: 'app_lot_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, LotRepository $repo): Response
    {
        $lot = $repo->find($id);
        if (!$lot) {
            throw $this->createNotFoundException('Lot introuvable.');
        }

        return $this->render('lot/show.html.twig', [
            'lot' => $lot,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/lot/{id}/bid', name: 'app_lot_bid', methods: ['POST'])]
    public function bid(int $id, Request $request, LotRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $lot = $repo->find($id);
        if (!$lot) {
            $this->addFlash('danger', 'Lot introuvable.');
            return new JsonResponse(['ok' => false, 'error' => 'Lot introuvable'], 404);
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('bid_lot_'.$lot->getId(), $token)) {
            $this->addFlash('danger', 'Erreur de sécurité (CSRF). Veuillez réessayer.');
            return new JsonResponse(['ok' => false, 'error' => 'CSRF'], 400);
        }

        $ev = $lot->getEvenementEnchere();
        if ($ev && (!$ev->estOuvert())) {
            $this->addFlash('warning', 'Enchères non ouvertes pour ce lot.');
            return new JsonResponse(['ok' => false, 'error' => 'Enchères non ouvertes'], 400);
        }

        $amount = (float) $request->request->get('amount', 0);

        $em->beginTransaction();
        try {
            $em->lock($lot, LockMode::PESSIMISTIC_WRITE);

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
