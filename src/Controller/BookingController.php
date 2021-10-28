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
    public function new(Request $request) : Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
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
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(Booking $booking, Request $request){
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
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