<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{


    /**
     * @Route ("/bookings", name="booking.index")
     * @param BookingRepository $repository
     * @return Response
     */
    public function index(BookingRepository $repository):Response
    {

        $bookings = $repository->findAll();
        return $this->render('pages/Booking/index.html.twig',[
            'current_menu' => 'booking',
            'bookings' =>$bookings
        ]);

    }

    /**
     * @Route ("/booking/create", name="booking.create")
     * @param Request $request
     * @return Response
     */
    public function new(BookingRepository $repository, Request $request) : Response
    {
        $booking = new Booking();
        $booking->initStartAndEndDate();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid() and $this->isBookingValid($repository, $booking)){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $this->addFlash('success','Reservation crée avec succès !');
            $entityManager->flush();

            return $this->redirectToRoute('booking.index');
        }
        return $this->render('pages/booking/new.html.twig', [
            'booking'=> $booking,
            'form'=> $form->createView(),
            'current_menu' => 'booking']);
    }

    /**
     * @Route ("/booking/{id}", name="booking.edit")
     * @param BookingRepository $repository
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(BookingRepository $repository, Booking $booking, Request $request){
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid() and $this->isBookingValid($repository, $booking)){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success','Votre modification à bien été enregistré !');
            return $this->redirectToRoute('booking.index');
        }
        return $this->render('pages/Booking/edit.html.twig', [
            'booking'=> $booking,
            'form'=> $form->createView(),
            'current_menu' => 'booking']);
    }

    public function isBookingValid(BookingRepository $repository, Booking $booking)
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
         * Si date de début < date debut alors  -> si date de fin <= date debut
         * Sinon si date debut >= date de fin good
         *
         */
        $existingBooking = $repository->findExistingBooking($booking);
        $resTimeSlot = true;
        /**
         * @var Booking $book
         */
        foreach ($existingBooking as $book)
        {
           if($booking->getStartDate()->format('H:i') < $book->getStartDate()->format('H:i')){
               if ($booking->getEndDate()->format('H:i')<= $book->getStartDate()->format('H:i')){
                   $resTimeSlot = true;
               } else {
                   $resTimeSlot = false;
                   $this->addFlash('error','Veuillez selectionner un autre creneau ou choisir un autre poste que :<b>'.$booking->getComputer()->getLabel().'</b> !');
               }
           } elseif (($booking->getStartDate()->format('H:i') >= $book->getEndDate()->format('H:i')))
               $resTimeSlot = true;
           else{
               $resTimeSlot = false;
               $this->addFlash('error','Veuillez selectionner un autre creneau ou choisir un autre poste que :<b>'.$booking->getComputer()->getLabel().'</b> !' );
           }
        }

        if ($resTimeSlot & $res) {
            return true;
        } else
          return false;
    }

    /**
     * @Route ("/booking/delete/{id}", name="booking.delete")
     * @param Booking $booking
     * @return Response
     */
    public function delete(Booking $booking, Request $request): Response
    {

        if($this->isCsrfTokenValid('delete'.$booking->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();
            $this->addFlash('success','Reservation supprimé avec succès !');
            return $this->redirectToRoute('booking.index',
                ['current_menu' => 'booking']);
        }

        return $this->redirectToRoute('booking.index',
            ['current_menu' => 'booking']);
    }
}