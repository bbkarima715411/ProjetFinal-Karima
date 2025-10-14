<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Form\DeposerOffreType;
use App\Repository\UserRepository;
use App\Service\GestionEncheres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur dédié au dépôt d'offres sur un lot.
 *
 * Flux recommandé pour créer des enchères: formulaire minimal puis
 * délégation au service `GestionEncheres`.
 */
#[Route('/encheres')]
class EnchereController extends AbstractController
{
    /**
     * Dépose une offre sur un `Lot`.
     *
     * Exige un utilisateur authentifié et un formulaire valide contenant `montant`.
     * En cas de succès, le service persiste l'enchère et applique la règle du minimum.
     */
    #[Route('/lot/{id}/offre', name: 'app_offre_sur_lot', methods: ['POST'])]
    public function deposerOffre(
        Lot $lot,
        Request $request,
        GestionEncheres $gestion,
        UserRepository $userRepo,
        EntityManagerInterface $em
    ): Response {
        // Nécessite un utilisateur connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère et valide le montant soumis
        $form = $this->createForm(DeposerOffreType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Formulaire invalide.');
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', "Utilisateur non identifié.");
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        $montant = (float)$form->get('montant')->getData();

        try {
            $gestion->deposerOffre($lot, $user, $montant);
            $this->addFlash('success', 'Offre déposée !');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
    }
}
