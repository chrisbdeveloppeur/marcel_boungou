<?php

namespace App\Repository;

use App\Entity\Biography;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Biography|null find($id, $lockMode = null, $lockVersion = null)
 * @method Biography|null findOneBy(array $criteria, array $orderBy = null)
 * @method Biography[]    findAll()
 * @method Biography[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiographyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Biography::class);
    }

    // /**
    //  * @return Biography[] Returns an array of Biography objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Biography
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
