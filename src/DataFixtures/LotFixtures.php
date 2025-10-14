<?php

namespace App\DataFixtures;

use App\Entity\Lot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Fixtures pour créer des lots de démonstration.
 *
 * Chaque lot reçoit un titre, une description, une catégorie,
 * un prix de départ, un incrément minimal et une date de fin.
 * Les lots sont rattachés aux événements créés par `EvenementEnchereFixtures`.
 */
class LotFixtures extends Fixture implements DependentFixtureInterface
{
    public const REF_PREFIX = 'lot_';
    public const COUNT = 10;

    /** Crée `COUNT` lots aléatoires et les rattache à un événement existant. */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Prépare les images de démonstration: on copie un fichier depuis public/images/demo vers public/uploads
        $demoDir   = __DIR__ . '/../../public/images/demo';
        $uploadDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }
        $demoImages = [];
        if (is_dir($demoDir)) {
            foreach (glob($demoDir.'/*.{jpg,jpeg,png,webp}', GLOB_BRACE) as $imgPath) {
                $demoImages[] = basename($imgPath);
            }
        }

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

        // Titres et descriptions personnalisés
        $titles = [
            'Fauteuil Louis XVI en noyer',
            'Bureau Art Déco en palissandre',
            'Commode Transition marquetée',
            'Huile sur toile – Paysage du XIXe',
            'Gravure d’après Dürer – Edition ancienne',
            'Affiche originale – Exposition 1925',
            'Bague en or et saphir, époque 1900',
            'Montre de poche gousset – Argent massif',
            'Tabatière en vermeil finement ciselée',
            'Pièce de 20 Francs Or Napoléon III',
        ];

        $descriptions = [
            'Élégant fauteuil d’époque, dossier médaillon, garniture refaite dans les règles de l’art. Quelques usures d’usage.',
            'Superbe bureau à caissons, lignes sobres typiques des années 30, placage en palissandre et poignées chromées.',
            'Belle commode galbée, riche marqueterie de fleurs, bronzes dorés. Restaurations anciennes, très bel état.',
            'Huile sur toile signée en bas à droite, scène bucolique, belle palette et profondeur. Cadre Montparnasse.',
            'Gravure sur papier vergé, marges conservées, tirage soigné. Légères rousseurs, sans incidence.',
            'Affiche lithographiée, couleurs vives, affiche d’exposition Internationale des Arts Décoratifs, 1925.',
            'Bague en or jaune 18k sertie d’un saphir ovale, entourage perlé. Poids brut 4,2 g. Taille 54.',
            'Montre de poche à remontage manuel, boîte en argent, cadran émaillé. En état de marche, légère fissure au verre.',
            'Tabatière en vermeil, décor de rinceaux. Poinçons lisibles, très joli travail d’orfèvrerie.',
            'Pièce de 20 Francs Or, Napoléon III, tête laurée. Poids 6,45 g, or 900‰. Traces légères de circulation.',
        ];

        for ($i = 0; $i < self::COUNT; $i++) {
            $lot = new Lot();
            $title = $titles[$i % count($titles)];
            $desc  = $descriptions[$i % count($descriptions)];
            $lot->setTitre($title)
                ->setDescription($desc)
                ->setCategorie($faker->randomElement($categories))
                ->setPrixDepart($faker->randomFloat(2, 50, 500))
                ->setIncrementMin($faker->randomFloat(2, 1, 10))
                ->setDateFin(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 day', '+3 weeks')))
                ->setEstVendu(false);

            $eventIndex = $faker->numberBetween(0, 2);
            $event = $this->getReference(EvenementEnchereFixtures::REF_PREFIX.$eventIndex);
            $lot->setEvenementEnchere($event);

            // Assigner une image de démonstration unique si disponible
            if (!empty($demoImages)) {
                $file = $faker->randomElement($demoImages);
                $src  = $demoDir . DIRECTORY_SEPARATOR . $file;
                $dst  = $uploadDir . DIRECTORY_SEPARATOR . $file;
                // Copier si pas déjà copié
                if (is_file($src) && !is_file($dst)) {
                    @copy($src, $dst);
                }
                // Enregistre uniquement le nom de fichier; l'affichage utilise asset('uploads/' ~ imageFilename)
                $lot->setImageFilename($file);
            }

            $manager->persist($lot);
            // Mémorise la référence pour pouvoir relier plus tard (ex: enchères) à ce lot
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
