<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Charge les catégories par défaut dans la base de données.
 * (à exécuter avec : php bin/console doctrine:fixtures:load -n)
 */
class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            // 🪑 Mobilier
            ['name' => 'Sièges & Fauteuils', 'slug' => 'sieges-fauteuils'],
            ['name' => 'Tables & Bureaux', 'slug' => 'tables-bureaux'],
            ['name' => 'Armoires & Commodes', 'slug' => 'armoires-commodes'],

            // 🎨 Art
            ['name' => 'Peintures anciennes', 'slug' => 'peintures-anciennes'],
            ['name' => 'Gravures & Dessins', 'slug' => 'gravures-dessins'],
            ['name' => 'Affiches anciennes', 'slug' => 'affiches-anciennes'],

            // 💎 Objets de valeur
            ['name' => 'Bijoux anciens', 'slug' => 'bijoux-anciens'],
            ['name' => 'Montres de collection', 'slug' => 'montres-collection'],
            ['name' => 'Objets précieux', 'slug' => 'objets-precieux'],
            ['name' => 'Monnaies & Médailles', 'slug' => 'monnaies-medailles'],

            // 🌍 Divers
            ['name' => 'Objets africains', 'slug' => 'objets-africains'],
            ['name' => 'Objets amérindiens', 'slug' => 'objets-amerindiens'],
        ];

        foreach ($categories as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setSlug($data['slug']);
            $category->setIsActive(true);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
