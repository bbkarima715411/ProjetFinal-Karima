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

        for ($i = 0; $i < self::COUNT; $i++) {
            $lot = new Lot();
            $lot->setLot(ucfirst($faker->unique()->word()))
                ->setCategorie($faker->randomElement(['art', 'electronique', 'maison', 'mode', 'collection']))
                ->setPaiement($faker->randomFloat(2, 20, 300))
                ->setFacture($faker->uuid());

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