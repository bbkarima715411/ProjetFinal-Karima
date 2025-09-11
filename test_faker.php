<?php

require_once 'vendor/autoload.php';

use Faker\Factory;

// Créer une instance de Faker en français
$faker = Factory::create('fr_FR');

echo "=== Test de Faker ===\n\n";

echo "Nom: " . $faker->name . "\n";
echo "Email: " . $faker->email . "\n";
echo "Téléphone: " . $faker->phoneNumber . "\n";
echo "Adresse: " . $faker->address . "\n";
echo "Ville: " . $faker->city . "\n";
echo "Texte: " . $faker->text(100) . "\n";
echo "Date: " . $faker->date('Y-m-d') . "\n";
echo "Prix: " . $faker->randomFloat(2, 10, 1000) . "€\n";

echo "\n=== Faker installé et fonctionnel ! ===\n";
