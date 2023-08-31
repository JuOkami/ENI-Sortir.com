<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\SortieFiltre;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{


    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Sortie::class);
        $this->entityManager = $entityManager;
    }

    // Méthode pour rechercher des sorties en fonction des critères
    public function findByRecherche(SortieFiltre $recherche, Participant $utilisateur)
    {
        $query = $this->createQueryBuilder('sortie')
            ->select("sortie");

        if (!empty($recherche->getName())) {
            $query = $query
                ->andWhere('sortie.nom LIKE :name')
                ->setParameter('name', "%{$recherche->getName()}%");
        }

        if (!empty($recherche->getSite())) {
            $query = $query
                ->andWhere('sortie.siteOrganisateur = :site')
                ->setParameter('site', $recherche->getSite());
        }

        if (!empty($recherche->getDateMin())) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut >= :datemin')
                ->setParameter('datemin', $recherche->getDateMin());
        }

        if (!empty($recherche->getDateMax())) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut <= :datemax')
                ->setParameter('datemax', $recherche->getDateMax());
        }

        if ($recherche->isIsOrganisateur()) {
            $query = $query
                ->andWhere('sortie.organisateur = :utilisateur')
                ->setParameter('utilisateur', $utilisateur);
        }

        if ($recherche->isIsInscrit() === null) {

        } else {
            if ($recherche->isIsInscrit()) {
                $query = $query
                    ->andWhere(':utilisateur MEMBER OF sortie.inscriptions')
                    ->setParameter('utilisateur', $utilisateur);
            }

            if (!$recherche->isIsInscrit()) {
                $query = $query
                    ->andWhere(':utilisateur NOT MEMBER OF sortie.inscriptions')
                    ->setParameter('utilisateur', $utilisateur);
            }
        }

        if ($recherche->isIsPasse()) {
            $query = $query
                ->andWhere('sortie.etat = :etat')
                ->setParameter('etat', 5);
        }

        $paginator = new Paginator($query);
        return $paginator;
    }

    // Méthode pour mettre à jour les états des sorties en fonction de la date et de la durée
    public function updateSortieStates(): void
    {
        $sorties = $this->findAll();

        // Obtention des états
        $openState = $this->getStateByLibelle('Ouverte');
        $closedState = $this->getStateByLibelle('Clôturée');
        $inProgressState = $this->getStateByLibelle('En cours');
        $pastState = $this->getStateByLibelle('Passée');
        $archivedState = $this->getStateByLibelle('Archivée');
        $currentDateTime = new DateTime();


        foreach ($sorties as $sortie) {
            // Mise à jour des états en fonction de la date et de la durée
            $endDateTime = clone $sortie->getDateHeureDebut();
            $endDateTime->add(new DateInterval('PT' . $sortie->getDuree() . 'H'));


            if ($sortie->getDateLimiteInscription() > $currentDateTime)
                $sortie->setEtat($openState);
            if ($sortie->getDateLimiteInscription() < $currentDateTime) {
                $sortie->setEtat($closedState);
            }
            if (($sortie->getDateHeureDebut() <= $currentDateTime) && ($currentDateTime <= $endDateTime)) {
                $sortie->setEtat($inProgressState);
            }
            if ($currentDateTime > $endDateTime) {
                $sortie->setEtat($pastState);
            }
            if ($sortie->getDateHeureDebut() < new DateTime('-1 month')) {
                $sortie->setEtat($archivedState);
            }

            $this->entityManager->persist($sortie);

        }

    }

    // Méthode privée pour obtenir l'état par libellé
    private function getStateByLibelle($libelle)

    {
        return $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => $libelle]);
    }


}
