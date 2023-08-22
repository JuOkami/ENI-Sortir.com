<?php

namespace App\Repository;

use App\Entity\SortieFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SortieFiltre>
 *
 * @method SortieFiltre|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortieFiltre|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortieFiltre[]    findAll()
 * @method SortieFiltre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieFiltreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortieFiltre::class);
    }

//    /**
//     * @return SortieFiltre[] Returns an array of SortieFiltre objects
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

//    public function findOneBySomeField($value): ?SortieFiltre
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
