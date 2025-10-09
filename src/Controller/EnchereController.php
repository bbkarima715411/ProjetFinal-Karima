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

#[Route('/encheres')]
class EnchereController extends AbstractController
{
    #[Route('/lot/{id}/offre', name: 'app_offre_sur_lot', methods: ['POST'])]
    public function deposerOffre(
        Lot $lot,
        Request $request,
        GestionEncheres $gestion,
        UserRepository $userRepo,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
