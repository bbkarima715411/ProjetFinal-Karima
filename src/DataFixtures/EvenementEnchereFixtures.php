<?php

namespace App\DataFixtures;

use App\Entity\EvenementEnchere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EvenementEnchereFixtures extends Fixture
{
    public const REF_PREFIX = 'event_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 3; $i++) {
            $event = new EvenementEnchere();
            $event->setEvenementEnchere($faker->randomFloat(2, 100, 10000));
            $manager->persist($event);

            $this->addReference(self::REF_PREFIX.$i, $event);
        }

        $manager->flush();
    }
}