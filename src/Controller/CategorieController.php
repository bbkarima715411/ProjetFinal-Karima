<?php

namespace App\Controller;

use App\Repository\LotRepository;
use App\Service\CategoryResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_categorie_show', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function show(string $slug, CategoryResolver $resolver, LotRepository $lots): Response
    {
        $label = $resolver->findLabelBySlug($slug);
        if (!$label) {
            throw $this->createNotFoundException('Catégorie inconnue.');
        }

        $result = $lots->findByCategoryLabel($label);

        return $this->render('categorie/show.html.twig', [
            'label' => $label,
            'lots'  => $result,
        ]);
    }
}
