<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\SortieFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
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
}
