<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Lot;
use App\Entity\EvenementEnchere;
use App\Entity\EnchereUtilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1) Créer quelques événements d'enchères
        $evenements = [];
        for ($i = 0; $i < 3; $i++) {
            $event = new EvenementEnchere();

            // >>> IMPORTANT <<< 
            // Dans TON code, EvenementEnchere::setEvenementEnchere attend un float.
            // On met donc un nombre (ex: "budget" / "valeur"), pas un texte.
            $event->setEvenementEnchere($faker->randomFloat(2, 100, 10000));

            $manager->persist($event);
            $evenements[] = $event;
        }

        // 2) Créer des utilisateurs
        $utilisateurs = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new Utilisateur();
            // Dans TON code: setUtilisateur(string|null)
            $user->setUtilisateur($faker->name());
            $manager->persist($user);
            $utilisateurs[] = $user;
        }

        // 3) Créer des lots, rattachés à un événement
        $lots = [];
        for ($i = 0; $i < 10; $i++) {
            $lot = new Lot();
            $lot->setLot(ucfirst($faker->unique()->word()))
                ->setCategorie($faker->randomElement(['art', 'electronique', 'maison', 'mode', 'collection']))
                ->setPaiement($faker->randomFloat(2, 20, 300))
                ->setFacture($faker->uuid())
                ->setEvenementEnchere($faker->randomElement($evenements)); // FK obligatoire

            $manager->persist($lot);
            $lots[] = $lot;
        }

        // 4) Créer des enchères d'utilisateurs sur les lots
        for ($i = 0; $i < 25; $i++) {
            $enchere = new EnchereUtilisateur();

            // Dans TON code: setEnchereUtilisateur(float|null)
            $enchere->setEnchereUtilisateur($faker->randomFloat(2, 50, 2000))
                    ->setLot($faker->randomElement($lots))
                    ->setUtilisateur($faker->randomElement($utilisateurs));

            $manager->persist($enchere);
        }

        $manager->flush();
    }
}
