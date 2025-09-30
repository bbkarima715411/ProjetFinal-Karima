<?php

namespace App\Controller;

use App\Repository\LotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private const CATEGORIES = [
        'sieges-fauteuils'   => 'Sièges & Fauteuils',
        'tables-bureaux'     => 'Tables & Bureaux',
        'armoires-commodes'  => 'Armoires & Commodes',

        'peintures-anciennes'=> 'Peintures anciennes',
        'gravures-dessins'   => 'Gravures & Dessins',
        'affiches-anciennes' => 'Affiches anciennes',

        'bijoux-anciens'     => 'Bijoux anciens',
        'montres-collection' => 'Montres de collection',
        'objets-precieux'    => 'Objets précieux',
        'monnaies-medailles' => 'Monnaies & Médailles',

        'objets-asiatiques'  => 'Objets asiatiques',
        'objets-africains'   => 'Objets africains',
        'objets-amerindiens' => 'Objets amérindiens',
    ];

    #[Route('/categorie/{slug}', name: 'app_categorie_show', requirements: ['slug' => '[a-z0-9\-]+'])]
    public function show(string $slug, LotRepository $lots): Response
    {
        $label = self::CATEGORIES[$slug] ?? null;
        if (!$label) {
            throw $this->createNotFoundException('Catégorie inconnue.');
        }

        // ⚠️ Ton entité Lot a une propriété "Categorie" (majuscule) + getCategorie()
        // On filtre donc sur la valeur lisible (ex: "Sièges & Fauteuils")
        $result = $lots->createQueryBuilder('l')
            ->andWhere('l.Categorie = :cat')
            ->setParameter('cat', $label)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('categorie/show.html.twig', [
            'slug'   => $slug,
            'label'  => $label,
            'lots'   => $result,
        ]);
    }
}
