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



    public function findExistingBooking(Booking $booking, $edit)
    {
         $qb = $this->createQueryBuilder('b');
         $qb->Where('b.Computer = :val')
            ->setParameter('val', $booking->getComputer());
        if ($edit)
            $qb->andWhere('b.id != :id')
                ->setParameter('id', $booking->getId());
        $qb->andwhere('YEAR(b.startDate) = :year')
            ->setParameter('year', $booking->getStartDate()->format('Y'))
            ->andWhere('MONTH(b.startDate) = :month')
            ->setParameter('month', $booking->getStartDate()->format('m'))
            ->andWhere('DAY(b.startDate) = :day')
            ->setParameter('day', $booking->getStartDate()->format('d'));
       return $qb->getQuery()
            ->getResult();
    }

    public function findCurrentBooking()
    {
        $dtNow = new \DateTime();

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
            ->orderBy('b.startDate', 'ASC')
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


    public function findBookingOfTheDay()
    {
        $dtNow = new \DateTime();

        // Je récupère toutes les reservation dont la date de fin et situé dans l'heure
        return  $this->createQueryBuilder('b')
            ->Where('YEAR(b.startDate) = :year')
            ->setParameter('year', $dtNow->format('Y'))
            ->andWhere('MONTH(b.startDate) = :month')
            ->setParameter('month', $dtNow->format('m'))
            ->andWhere('DAY(b.startDate) = :day')
            ->setParameter('day', $dtNow->format('d'))
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDesc()
    {
        return $this->createQueryBuilder('b')

            ->orderBy('b.startDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAsc()
    {
        return $this->createQueryBuilder('b')

            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
