<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFacturesController extends AbstractController
{
    #[Route('/mes/factures', name: 'app_mes_factures')]
    public function index(): Response
    {
        return $this->render('mes_factures/index.html.twig', [
            'controller_name' => 'MesFacturesController',
        ]);
    }
}
