<?php

namespace App\Command;

use App\Entity\Etat;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'UpdateArchivedSorties',
    description: 'Add a short description for your command',
)]
class UpdateArchivedSortiesCommand extends Command
{


    private SortieRepository $sortieRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->sortieRepository = $sortieRepository;
        $this->entityManager = $entityManager;
    }
    protected function configure(): void
    {
        $this->setDescription('Update archived sorties older than a month')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // Mettre à jour l'état des sorties archivées
        $archivedSorties = $this->sortieRepository->findArchivableSorties();
        $archivedState = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']);

        foreach ($archivedSorties as $sortie) {
            if ($sortie->getDateHeureDebut() < new DateTime('-1 month')) {
                $sortie->setEtat($archivedState);
                $this->entityManager->persist($sortie);

                $this->entityManager->flush();
                $output->writeln('Archived sorties updated.');
            }
        }
        return Command::SUCCESS;
    }
}
