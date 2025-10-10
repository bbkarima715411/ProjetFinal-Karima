<?php

namespace App\Command;

use App\Entity\EvenementEnchere;
use App\Repository\EvenementEnchereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reset-encheres-jour',
    description: 'Remet à jour les enchères chaque jour (08h00 → 20h00)',
)]
class ResetEncheresJourCommand extends Command
{
    public function __construct(
        private EvenementEnchereRepository $repo,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tz = new \DateTimeZone('Europe/Bruxelles');
        $debut = EvenementEnchere::debutDuJour($tz);
        $fin = EvenementEnchere::finDuJour($tz);

        $evenements = $this->repo->findAll();

        foreach ($evenements as $ev) {
            $ev->setDebutAt($debut);
            $ev->setFinAt($fin);
            $ev->setStatut('ouvert'); // juste pour affichage
        }

        $this->em->flush();

        $output->writeln(sprintf(
            "✅ %d enchère(s) réinitialisée(s) pour aujourd’hui (%s → %s).",
            count($evenements),
            $debut->format('d/m/Y H:i'),
            $fin->format('d/m/Y H:i')
        ));

        return Command::SUCCESS;
    }
}
