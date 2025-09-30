<?php

namespace App\Controller;

use App\Repository\EvenementEnchereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(EvenementEnchereRepository $repo): Response
    {
        // Version "safe" (évite finAt/debutAt si tes champs ne sont pas encore en BDD)
        $aVenir = $repo->findBy([], ['id' => 'DESC'], 20);

        return $this->render('accueil/index.html.twig', [
            'evenementsAVenir' => $aVenir,
        ]);
    }
}
