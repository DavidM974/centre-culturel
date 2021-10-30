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

    public function findCurrentBooking()
    {
        $dtNow = new \DateTime();
        $dtNow->setTimezone(new \DateTimeZone('GMT+4'));

        // Je récupère toutes les reservation dont la date de fin et situé dans l'heure
        $res = $this->createQueryBuilder('b')
            ->Where('YEAR(b.startDate) = :year')
            ->setParameter('year',$dtNow->format('Y'))
            ->andWhere('MONTH(b.startDate) = :month')
            ->setParameter('month', $dtNow->format('m'))
            ->andWhere('DAY(b.startDate) = :day')
            ->setParameter('day', $dtNow->format('d'))
            ->andWhere('HOUR(b.endDate) >= :hour')
            ->setParameter('hour', $dtNow->format('H'))
            ->getQuery()
            ->getResult()
            ;
        // Je supprimer les reservation qui ne sont pas encore commencé dans l'heure et celle qui sont terminées
        foreach ($res as $key => $booking)
            {
                if (($booking->getStartDate()->format('H') == $dtNow->format('H') &&
                    $booking->getStartDate()->format('i') > $dtNow->format('i')) OR
                    ($booking->getEndDate()->format('H') == $dtNow->format('H') &&
                        $booking->getEndDate()->format('i') < $dtNow->format('i'))){
                    unset($res[$key]);
                }
            }
        return $res;
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
