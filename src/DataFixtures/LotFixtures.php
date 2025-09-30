<?php

namespace App\DataFixtures;

use App\Entity\Lot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LotFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_PREFIX = 'lot_';
    public const COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = [
            'Sièges & Fauteuils',
            'Tables & Bureaux', 
            'Armoires & Commodes',
            'Peintures anciennes',
            'Gravures & Dessins',
            'Affiches anciennes',
            'Bijoux anciens',
            'Montres de collection',
            'Objets précieux',
            'Monnaies & Médailles',
            'Objets asiatiques',
            'Objets africains',
            'Objets amérindiens'
        ];

        for ($i = 0; $i < self::COUNT; $i++) {
            $lot = new Lot();
            $lot->setLot(ucfirst($faker->unique()->word()))
                ->setCategorie($faker->randomElement($categories))
                ->setPaiement($faker->randomFloat(2, 20, 300))
                ->setFacture($faker->uuid())
                ->setPrixDepart($faker->randomFloat(2, 50, 500))
                ->setIncrementMin($faker->randomFloat(2, 1, 10));

            // Associer à un événement aléatoire via les références de EvenementEnchereFixtures
            $eventIndex = $faker->numberBetween(0, 2);
            $event = $this->getReference(EvenementEnchereFixtures::REF_PREFIX.$eventIndex);
            $lot->setEvenementEnchere($event);

            $manager->persist($lot);

            $this->addReference(self::REF_PREFIX.$i, $lot);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EvenementEnchereFixtures::class,
        ];
    }
}