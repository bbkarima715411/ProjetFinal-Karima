<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFacturesController extends AbstractController
{
    #[Route('/mes/factures', name: 'app_mes_factures')]
    public function index(OrderRepository $orderRepository): Response
    {
        // Nécessite un utilisateur connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer les commandes de cet utilisateur, les plus récentes en premier
        $orders = $orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('mes_factures/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
