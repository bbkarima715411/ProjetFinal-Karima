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
        // Simple: derniers events, on affichera leur titre dans le carrousel
        $evenementsEnCours = $repo->findBy([], ['id' => 'DESC'], 8);

        return $this->render('accueil/index.html.twig', [
            'evenementsEnCours' => $evenementsEnCours,
        ]);
    }
}
