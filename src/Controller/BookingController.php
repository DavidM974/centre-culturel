<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{


    /**
     * @Route ("/admin/bookings", name="booking.index")
     * @param BookingRepository $repository
     * @return Response
     */
    public function index(BookingRepository $repository):Response
    {

        $bookings = $repository->findAsc();
        return $this->render('pages/Booking/index.html.twig',[
            'current_menu' => 'booking',
            'bookings' =>$bookings
        ]);

    }

    /**
     * @Route ("/admin/bookings/day", name="booking.day")
     * @param BookingRepository $repository
     * @return Response
     */
    public function dayBooking(BookingRepository $repository):Response
    {

        $bookings = $repository->findBookingOfTheDay();
        return $this->render('pages/Booking/index.html.twig',[
            'current_menu' => 'booking',
            'bookings' =>$bookings
        ]);

    }

    /**
     * @Route ("/admin/prebooking", name="booking.pre")
     * @param BookingRepository $repository
     * @return Response
     */
    public function preBooking(BookingRepository $repository):Response
    {

        return $this->render('pages/User/preBooking.html.twig',[
            'current_menu' => 'booking',
        ]);

    }

    /**
     * @Route ("/admin/booking/create", name="booking.create")
     * @param Request $request
     * @return Response
     */
    public function new(BookingRepository $repository, Request $request) : Response
    {
        $booking = new Booking();
        $booking->initStartAndEndDate();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid() and $this->isBookingValid($repository, $booking, false)){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $this->addFlash('success','Reservation cr??e avec succ??s !');
            $entityManager->flush();

            return $this->redirectToRoute('booking.index');
        }
        return $this->render('pages/booking/new.html.twig', [
            'booking'=> $booking,
            'form'=> $form->createView(),
            'current_menu' => 'booking',
            'hourly' => Booking::TIMESLOT_ARRAY,
            'week' => Booking::WEEKDAY]);
    }

    /**
     * @Route ("/admin/booking/{id}", name="booking.edit")
     * @param BookingRepository $repository
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(BookingRepository $repository, Booking $booking, Request $request){
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid() and $this->isBookingValid($repository, $booking, true)){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success','Votre modification ?? bien ??t?? enregistr?? !');
            return $this->redirectToRoute('booking.index');
        }
        return $this->render('pages/Booking/edit.html.twig', [
            'booking'=> $booking,
            'form'=> $form->createView(),
            'current_menu' => 'booking',
            'hourly' => Booking::TIMESLOT_ARRAY,
            'week' => Booking::WEEKDAY]);
    }

    public function isBookingValid(BookingRepository $repository, Booking $booking, $edit)
    {
        if ($booking->isTimeSlotValid()) {
            $res = true;
        } else {
            $this->addFlash('error','Veuillez respecter les horaire de reservations !');
            $res = false;
        }
        /*
         * Recupere toute les reservation du pc sur la date
         * boucle
         * Si date de d??but < date debut alors  -> si date de fin <= date debut
         * Sinon si date debut >= date de fin good
         *
         */
        $existingBooking = $repository->findExistingBooking($booking, $edit);
        $resTimeSlot = true;

        /**
         * @var Booking $book
         */
        foreach ($existingBooking as $book)
        {
            //si date debut NewResa inferieur  a  date debut existingResa
           if($booking->getStartDate()->format('H:i') < $book->getStartDate()->format('H:i')){
               //si date fin NewResa inferieur  a date debut existingResa
               if ($booking->getEndDate()->format('H:i')<= $book->getStartDate()->format('H:i')){
                   $resTimeSlot = true;
               } else {
                   $this->addFlash('error','Le creaneau horaire suivant '.$book->getStartDate()->format('H:i').' - '.$book->getEndDate()->format('H:i').' est d??j?? pris pour le poste suivant : '.$booking->getComputer()->getLabel().' !' );
                    return false;
               }
           } elseif (($booking->getStartDate()->format('H:i') >= $book->getEndDate()->format('H:i'))){
               $resTimeSlot = true;
           } else{
               $this->addFlash('error','Le creaneau horaire suivant '.$book->getStartDate()->format('H:i').' - '.$book->getEndDate()->format('H:i').' est d??j?? pris pour le poste suivant : '.$booking->getComputer()->getLabel().' !' );
               return false;
           }
        }

        if ($resTimeSlot & $res) {
            return true;
        } else
          return false;
    }

    /**
     * @Route ("/admin/booking/delete/{id}", name="booking.delete")
     * @param Booking $booking
     * @return Response
     */
    public function delete(Booking $booking, Request $request): Response
    {

        if($this->isCsrfTokenValid('delete'.$booking->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();
            $this->addFlash('success','Reservation supprim?? avec succ??s !');
            if ( $request->get('_redirect') == 'DASHBOARD')
                return $this->redirectToRoute('dashboard',['current_menu' => 'dashboard']);
            else
                return $this->redirectToRoute('booking.index',['current_menu' => 'booking']);
        }

        return $this->redirectToRoute('booking.index',
            ['current_menu' => 'booking']);
    }
}