<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\FavoriRepository;

/**
 * Page listant les favoris de l'utilisateur connecté.
 */
#[IsGranted('ROLE_USER')]
class MesFavorisController extends AbstractController
{
    #[Route('/mes/favoris', name: 'app_mes_favoris')]
    public function index(FavoriRepository $favoriRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $favoris = $favoriRepo->findByUser($user);

        return $this->render('mes_favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }
}
