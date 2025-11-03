<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-categories',
    description: 'Ajoute des catégories par défaut dans la base de données',
)]
class AddCategoriesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categories = [
            'Électronique',
            'Vêtements',
            'Maison',
            'Jouets',
            'Sport',
            'Loisirs',
            'Informatique',
            'Téléphonie',
            'Beauté',
            'Alimentation'
        ];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        $io->success(sprintf('Les %d catégories ont été ajoutées avec succès.', count($categories)));

        return Command::SUCCESS;
    }
}
