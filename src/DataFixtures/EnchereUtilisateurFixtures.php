<?php

namespace App\DataFixtures;

use App\Entity\EnchereUtilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EnchereUtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    public const COUNT = 25;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::COUNT; $i++) {
            $enchere = new EnchereUtilisateur();

            // Montant aléatoire
            $enchere->setMontant($faker->randomFloat(2, 50, 2000));

            // Associer un lot existant via les références de LotFixtures
            $lotIndex = $faker->numberBetween(0, LotFixtures::COUNT - 1);
            $lot = $this->getReference(LotFixtures::REF_PREFIX.$lotIndex);
            $enchere->setLot($lot);

            // Associer un utilisateur existant via les références de UtilisateurFixtures
            $userIndex = $faker->numberBetween(0, 9);
            $user = $this->getReference(UtilisateurFixtures::REF_PREFIX.$userIndex);
            $enchere->setUtilisateur($user);

            $manager->persist($enchere);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
            LotFixtures::class,
        ];
    }
}
