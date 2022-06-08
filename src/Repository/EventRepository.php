<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    private $today;
    public function __construct(ManagerRegistry $registry)
    {
        $this->today = new \DateTime('now');
        parent::__construct($registry, Event::class);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */

    public function findNextEvents()
    {
        return $this->createQueryBuilder('e')
            ->setParameter('today', $this->today)
            ->andWhere('e.datetime >= :today ')
            ->orderBy('e.datetime', 'ASC')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPastedEvents()
    {
        return $this->createQueryBuilder('e')
            ->setParameter('today', $this->today)
            ->andWhere('e.datetime < :today ')
            ->orderBy('e.datetime', 'ASC')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
