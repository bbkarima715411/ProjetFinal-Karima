<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:make-admin',
    description: 'Définit un utilisateur comme administrateur',
)]
class MakeAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur à promouvoir administrateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('Aucun utilisateur trouvé avec l\'email %s', $email));
            return Command::FAILURE;
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            $this->entityManager->flush();
            $io->success(sprintf('L\'utilisateur %s est maintenant administrateur', $email));
        } else {
            $io->success(sprintf('L\'utilisateur %s est déjà administrateur', $email));
        }

        return Command::SUCCESS;
    }
}
