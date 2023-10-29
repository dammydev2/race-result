<?php

namespace App\Repository;

use App\Entity\Tryy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tryy>
 *
 * @method Tryy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tryy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tryy[]    findAll()
 * @method Tryy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TryyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tryy::class);
    }

//    /**
//     * @return Tryy[] Returns an array of Tryy objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tryy
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
