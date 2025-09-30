<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\Utilisateur;
use App\Form\DeposerOffreType;
use App\Repository\UtilisateurRepository;
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
        UtilisateurRepository $uRepo,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(DeposerOffreType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Formulaire invalide.');
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        // mappe l’utilisateur sécurité -> entité Utilisateur de ton domaine (on stocke l’email dans "Utilisateur")
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $email = $user?->getUserIdentifier();
        if (!$email) {
            $this->addFlash('error', "Utilisateur non identifié.");
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        $utilisateur = $uRepo->findOneBy(['Utilisateur' => $email]);
        if (!$utilisateur) {
            $utilisateur = (new Utilisateur())->setUtilisateur($email);
            $em->persist($utilisateur);
            $em->flush();
        }

        $montant = (float)$form->get('montant')->getData();

        try {
            $gestion->deposerOffre($lot, $utilisateur, $montant);
            $this->addFlash('success', 'Offre déposée !');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
    }
}
