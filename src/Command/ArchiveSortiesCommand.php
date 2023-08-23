<?php

namespace App\Command;

use App\Entity\SortieArchive;
use App\Repository\SortieArchiveRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// Utilisation de l'attribut AsCommand pour définir le nom et la description de la commande
#[AsCommand(
    name: 'ArchiveSorties',
    description: 'Add a short description for your command',
)]
class ArchiveSortiesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private SortieRepository $sortieRepository;
    private SortieArchiveRepository $sortieArchiveRepository;

    // Injection des dépendances dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, SortieArchiveRepository $sortieArchiveRepository)
    {
        $this->entityManager = $entityManager;
        $this->sortieRepository = $sortieRepository;
        $this->sortieArchiveRepository = $sortieArchiveRepository;

        parent::__construct();
    }

    // Configuration de la commande
    protected function configure(): void
    {
        $this
            ->setDescription("Archive les sortie passées depuis plus d'un mois.")
        ;
    }

    // Méthode exécutée lors de l'exécution de la commande
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Création d'une date correspondant à il y a un mois
        $oneMonthAgo = new \DateTime();
        $oneMonthAgo->modify('-1 month');

        // Récupération des sorties à archiver depuis le repository
        $sortiesToArchive = $this->sortieRepository->findSortiesToArchive($oneMonthAgo);

        // Parcours des sorties à archiver et marquage comme archivées
        foreach ($sortiesToArchive as $sortie){
            $sortieArchive = new SortieArchive();
            $sortieArchive->cloneSortie($sortie);

            $this->entityManager->persist($sortieArchive);
            $this->entityManager->remove($sortie);
            $this->entityManager->flush($sortieArchive);
            $this->entityManager->flush($sortie);
        }

        // Affichage du nombre de sorties archivées
        $output->writeln(count($sortiesToArchive). ' sorties ont été archivées');

        // La méthode execute doit retourner un entier
        return Command::SUCCESS;
    }
}
