<?php

namespace App\Controller;

use App\Repository\EnchereUtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesEncheresController extends AbstractController
{
    #[Route('/mes/encheres', name: 'app_mes_encheres')]
    public function index(EnchereUtilisateurRepository $enchereRepo): Response
    {
        // Nécessite un utilisateur connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer les enchères de cet utilisateur (plus récentes en premier)
        $encheres = $enchereRepo->createQueryBuilder('e')
            ->leftJoin('e.lot', 'l')
            ->addSelect('l')
            ->andWhere('e.user = :u')
            ->setParameter('u', $user)
            ->orderBy('e.creeLe', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('mes_encheres/index.html.twig', [
            'encheres' => $encheres,
        ]);
    }
}
