<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesEncheresController extends AbstractController
{
    #[Route('/mes/encheres', name: 'app_mes_encheres')]
    public function index(): Response
    {
        return $this->render('mes_encheres/index.html.twig', [
            'controller_name' => 'MesEncheresController',
        ]);
    }
}
