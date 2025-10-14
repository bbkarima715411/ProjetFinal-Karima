<?php

namespace App\Controller;

use App\Entity\EnchereUtilisateur;
use App\Repository\EnchereUtilisateurRepository;
use App\Form\EnchereUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contrôleur CRUD des enchères utilisateurs.
 *
 * Restreint aux administrateurs. Pour déposer une offre côté utilisateur,
 * utiliser le flux métier via `EnchereController::deposerOffre()`.
 */
#[Route('/enchere/utilisateur')]
#[IsGranted('ROLE_ADMIN')]
class EnchereUtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_enchere_utilisateur_index', methods: ['GET'])]
    /** Liste les enchères (admin) */
    public function index(EnchereUtilisateurRepository $enchereUtilisateurRepository): Response
    {
        return $this->render('enchere_utilisateur/index.html.twig', [
            'enchere_utilisateurs' => $enchereUtilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_enchere_utilisateur_new', methods: ['GET', 'POST'])]
    /** Création désactivée: redirige vers l'index des lots */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Désactivé: on force l'utilisation du flux métier d'offre via la page du lot
        $this->addFlash('info', 'Création d\'enchère via CRUD désactivée. Déposez une offre depuis la page du lot.');
        return $this->redirectToRoute('app_lot_index');
    }

    #[Route('/{id}', name: 'app_enchere_utilisateur_show', methods: ['GET'])]
    /** Affiche le détail d'une enchère (admin) */
    public function show(EnchereUtilisateur $enchereUtilisateur): Response
    {
        return $this->render('enchere_utilisateur/show.html.twig', [
            'enchere_utilisateur' => $enchereUtilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enchere_utilisateur_edit', methods: ['GET', 'POST'])]
    /** Édite une enchère (admin) */
    public function edit(Request $request, EnchereUtilisateur $enchereUtilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnchereUtilisateurType::class, $enchereUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_enchere_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enchere_utilisateur/edit.html.twig', [
            'enchere_utilisateur' => $enchereUtilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enchere_utilisateur_delete', methods: ['POST'])]
    /** Supprime une enchère (admin) */
    public function delete(Request $request, EnchereUtilisateur $enchereUtilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enchereUtilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($enchereUtilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enchere_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
