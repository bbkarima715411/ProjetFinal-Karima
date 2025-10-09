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

#[Route('/enchere/utilisateur')]
class EnchereUtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_enchere_utilisateur_index', methods: ['GET'])]
    public function index(EnchereUtilisateurRepository $enchereUtilisateurRepository): Response
    {
        return $this->render('enchere_utilisateur/index.html.twig', [
            'enchere_utilisateurs' => $enchereUtilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_enchere_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $enchereUtilisateur = new EnchereUtilisateur();
        $form = $this->createForm(EnchereUtilisateurType::class, $enchereUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($enchereUtilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_enchere_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enchere_utilisateur/new.html.twig', [
            'enchere_utilisateur' => $enchereUtilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enchere_utilisateur_show', methods: ['GET'])]
    public function show(EnchereUtilisateur $enchereUtilisateur): Response
    {
        return $this->render('enchere_utilisateur/show.html.twig', [
            'enchere_utilisateur' => $enchereUtilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enchere_utilisateur_edit', methods: ['GET', 'POST'])]
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
    public function delete(Request $request, EnchereUtilisateur $enchereUtilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enchereUtilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($enchereUtilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enchere_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
