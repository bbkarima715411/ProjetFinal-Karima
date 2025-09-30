<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UtilisateurFixtures extends Fixture
{
    public const REF_PREFIX = 'user_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $user = new Utilisateur();
            $user->setUtilisateur($faker->name());
            $manager->persist($user);

            // Enregistrer une référence pour réutiliser ces utilisateurs dans d'autres fixtures
            $this->addReference(self::REF_PREFIX.$i, $user);
        }

        $manager->flush();
    }
}
