<?php
namespace App\Controller;

use App\Repository\EvenementEnchereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Je suis le contrôleur de la page d'accueil, je gère l'affichage de la page principale du site
class AccueilController extends AbstractController
{
    // Je mappe la route de la page d'accueil sur l'URL "/"
    #[Route('/', name: 'app_accueil')]
    public function index(EvenementEnchereRepository $repo): Response
    {
        // Je récupère les 8 derniers évènements d'enchères pour les afficher sur le carrousel de la page d'accueil
        $evenementsEnCours = $repo->findBy([], ['id' => 'DESC'], 8);

        // Je passe la liste des évènements à mon template d'accueil
        return $this->render('accueil/index.html.twig', [
            'evenementsEnCours' => $evenementsEnCours,
        ]);
    }
}
