<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }



    public function findExistingBooking(Booking $booking)
    {
        return $this->createQueryBuilder('b')
            ->Where('b.Computer = :val')
            ->setParameter('val', $booking->getComputer())
            ->andWhere('b.id != :id')
            ->setParameter('id', $booking->getId())
            ->andwhere('YEAR(b.startDate) = :year')
            ->setParameter('year', $booking->getStartDate()->format('Y'))
            ->andWhere('MONTH(b.startDate) = :month')
            ->setParameter('month', $booking->getStartDate()->format('m'))
            ->andWhere('DAY(b.startDate) = :day')
            ->setParameter('day', $booking->getStartDate()->format('d'))
            ->getQuery()
            ->getResult()
        ;

    }


    /*
    public function findOneBySomeField($value): ?Booking
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
