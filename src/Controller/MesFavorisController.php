<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFavorisController extends AbstractController
{
    #[Route('/mes/favoris', name: 'app_mes_favoris')]
    public function index(): Response
    {
        return $this->render('mes_favoris/index.html.twig', [
            'controller_name' => 'MesFavorisController',
        ]);
    }
}
