<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Form\DeposerOffreType;
use App\Repository\LotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lots')]
class LotController extends AbstractController
{
    #[Route('', name: 'app_lot_index')]
    public function index(LotRepository $repo): Response
    {
        $lots = $repo->findBy([], ['id' => 'DESC']); // ou ta méthode maison
        return $this->render('lot/index.html.twig', [
            'lots' => $lots,
        ]);
    }

    #[Route('/{id}', name: 'app_lot_show', requirements: ['id' => '\d+'])]
    public function show(Lot $lot): Response
    {
        $formOffre = $this->createForm(DeposerOffreType::class);

        return $this->render('lot/show.html.twig', [
            'lot' => $lot,
            'formOffre' => $formOffre->createView(),
        ]);
    }
}

