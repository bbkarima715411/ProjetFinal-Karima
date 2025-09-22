<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Form\LotType;
use App\Repository\LotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/lot')]
class LotController extends AbstractController
{
    #[Route('/', name: 'app_lot_index', methods: ['GET'])]
    public function index(LotRepository $lotRepository): Response
    {
        return $this->render('lot/index.html.twig', [
            'lots' => $lotRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lot_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $lot = new Lot();
        $form = $this->createForm(LotType::class, $lot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('imageFile')->getData();

            if ($file instanceof UploadedFile) {
                $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($original);
                $ext = $file->guessExtension() ?: 'bin';
                $newName = sprintf('%s-%s.%s', $safe, uniqid('', true), $ext);

                try {
                    $file->move($this->getParameter('upload_dir'), $newName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Upload impossible : ' . $e->getMessage());
                    // Optionally, you could redirect back to form here
                }

                if (method_exists($lot, 'setImageFilename')) {
                    $lot->setImageFilename($newName);
                }
            }

            $entityManager->persist($lot);
            $entityManager->flush();

            $this->addFlash('success', 'Lot enregistré ✔');

            return $this->redirectToRoute('app_lot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('lot/new.html.twig', [
            'lot' => $lot,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lot_show', methods: ['GET'])]
    public function show(Lot $lot): Response
    {
        return $this->render('lot/show.html.twig', [
            'lot' => $lot,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lot_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lot $lot, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LotType::class, $lot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lot_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('lot/edit.html.twig', [
            'lot' => $lot,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lot_delete', methods: ['POST'])]
    public function delete(Request $request, Lot $lot, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lot->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lot_index', [], Response::HTTP_SEE_OTHER);
    }
}

