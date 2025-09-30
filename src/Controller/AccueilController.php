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
        $now = new \DateTimeImmutable();
        $aVenir = $repo->createQueryBuilder('e')
            ->andWhere('e.finAt > :now')
            ->setParameter('now', $now)
            ->orderBy('e.debutAt', 'ASC')
            ->setMaxResults(20)
            ->getQuery()->getResult();

        return $this->render('accueil/index.html.twig', [
            'evenementsAVenir' => $aVenir,
        ]);
    }
}

