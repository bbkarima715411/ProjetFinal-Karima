<?php

namespace App\Controller;

use App\Entity\EvenementEnchere;
use App\Form\EvenementEnchereType;
use App\Repository\EvenementEnchereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement/enchere')]
class EvenementEnchereController extends AbstractController
{
    #[Route('/', name: 'app_evenement_enchere_index', methods: ['GET'])]
    public function index(EvenementEnchereRepository $evenementEnchereRepository): Response
    {
        return $this->render('evenement_enchere/index.html.twig', [
            'evenement_encheres' => $evenementEnchereRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_enchere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenementEnchere = new EvenementEnchere();
        $form = $this->createForm(EvenementEnchereType::class, $evenementEnchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenementEnchere);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_enchere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement_enchere/new.html.twig', [
            'evenement_enchere' => $evenementEnchere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_enchere_show', methods: ['GET'])]
    public function show(EvenementEnchere $evenementEnchere): Response
    {
        return $this->render('evenement_enchere/show.html.twig', [
            'evenement_enchere' => $evenementEnchere,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_enchere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EvenementEnchere $evenementEnchere, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementEnchereType::class, $evenementEnchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_enchere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement_enchere/edit.html.twig', [
            'evenement_enchere' => $evenementEnchere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_enchere_delete', methods: ['POST'])]
    public function delete(Request $request, EvenementEnchere $evenementEnchere, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenementEnchere->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenementEnchere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_enchere_index', [], Response::HTTP_SEE_OTHER);
    }
}
