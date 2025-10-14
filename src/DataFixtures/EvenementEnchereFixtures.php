<?php

namespace App\DataFixtures;

use App\Entity\EvenementEnchere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Fixtures pour créer quelques événements d'enchères de démonstration.
 *
 * Chaque événement reçoit un titre, des dates aléatoires et un statut factice.
 */
class EvenementEnchereFixtures extends Fixture
{
    public const REF_PREFIX = 'event_';

    /**
     * Charge 3 événements avec des dates/labels aléatoires
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $titles = [
            'Vente aux enchères - Bijoux anciens',
            'Collection d\'art moderne',
            'Mobilier d\'époque'
        ];

        for ($i = 0; $i < 3; $i++) {
            $event = new EvenementEnchere();
            $event->setTitre($titles[$i])
                ->setDebutAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')))
                ->setFinAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 week', '+3 weeks')))
                ->setStatut($faker->randomElement(['programmé', 'ouvert', 'clos']));
            
            $manager->persist($event);
            // Enregistre une référence pour que d'autres fixtures puissent relier un lot à un événement
            $this->addReference(self::REF_PREFIX.$i, $event);
        }

        $manager->flush();
    }
}