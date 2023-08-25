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


    public function findByRecherche(SortieFiltre $recherche, Participant $utilisateur ){
        $query =  $this->createQueryBuilder('sortie')
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

        if ($recherche->isIsOrganisateur()){
            $query = $query
                ->andWhere('sortie.organisateur = :utilisateur')
                ->setParameter('utilisateur', $utilisateur);
        }

        if($recherche->isIsInscrit() === null){

        }
        else{
            if ($recherche->isIsInscrit()){
                $query = $query
                    ->andWhere(':utilisateur MEMBER OF sortie.inscriptions')
                    ->setParameter('utilisateur', $utilisateur);
            }

            if (!$recherche->isIsInscrit()){
                $query = $query
                    ->andWhere(':utilisateur NOT MEMBER OF sortie.inscriptions')
                    ->setParameter('utilisateur', $utilisateur);
            }
        }

        if ($recherche->isIsPasse()){
            $query = $query
                ->andWhere('sortie.etat = :etat')
                ->setParameter('etat', 5);
        }

        $paginator = new Paginator($query);
        return $paginator;
    }

    public function updateSortieStates(): void
    {
        $sorties = $this->findAll();

        $openState = $this->getStateByLibelle('Ouverte');
        $closedState = $this->getStateByLibelle('Clôturée');
        $inProgressState = $this->getStateByLibelle('En cours');
        $pastState = $this->getStateByLibelle('Passée');
        $archivedState = $this->getStateByLibelle('Archivée');
        $currentDateTime = new DateTime();


        foreach ($sorties as $sortie) {
            $endDateTime = clone $sortie->getDateHeureDebut();
            $endDateTime->add(new DateInterval('PT'. $sortie->getDuree() .'H'));


            if ($sortie->getDateLimiteInscription()> $currentDateTime)
                $sortie->setEtat($openState);
            if ($sortie->getDateLimiteInscription() < $currentDateTime) {
                $sortie->setEtat($closedState);
            } if (($sortie->getDateHeureDebut() <= $currentDateTime) && ($currentDateTime <= $endDateTime)) {
                $sortie->setEtat($inProgressState);
            } if ($currentDateTime > $endDateTime) {
                $sortie->setEtat($pastState);
            } if ($sortie->getDateHeureDebut() < new DateTime('-1 month')) {
                $sortie->setEtat($archivedState);
            }

            $this->entityManager->persist($sortie);

        }

    }

//    public function findArchivableSorties()
//    {
//        return $this->createQueryBuilder('s')
//            ->where('s.dateHeureDebut < :date')
//            ->setParameter('date', new DateTime('-1 month'))
//            ->getQuery()
//            ->getResult();
//    }
//
//    public function findClosedSorties()
//    {
//        return $this->createQueryBuilder('s')
//            ->where(':date < s.dateLimiteInscription')
//            ->setParameter('date', new DateTime())
//            ->getQuery()
//            ->getResult();
//    }
//    public function findinProgressSorties()
//    {
//        return $this->createQueryBuilder('s')
//            ->where('s.dateHeureDebut < :date')
//            ->setParameter('date', new DateTime)
//            ->getQuery()
//            ->getResult();
//    }
//    public function findPastSorties()
//    {
//        return $this->createQueryBuilder('s')
//            ->where(':date > (s.dateHeureDebut + s.duree)')
//            ->setParameter('date', new DateTime)
//            ->getQuery()
//            ->getResult();
//    }

    //    public function findNonArchivedSorties($archivedState)
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.etat != :archivedState')
//            ->setParameter('archivedState', $archivedState)
//            ->getQuery()
//            ->getResult();
//    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    private function getStateByLibelle($libelle)

    {
        return $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => $libelle]);
    }


}
