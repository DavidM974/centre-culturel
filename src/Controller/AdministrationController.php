<?php

namespace App\Controller;

use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrationController extends AbstractController
{

    /**
     * @Route ("/admin", name="administration")
     * @return Response
     */
    public function index():Response
    {

                return $this->render('pages/administration.html.twig',[
                    'current_menu' => 'admin'
                ]);

    }

    /**
     * @Route ("/showbooking", name="showbooking")
     * @param ComputerRepository $repository
     * @return Response
     */
    public function showBooking(ComputerRepository $repository):Response
    {
        $computers = $repository->findAll();
        return $this->render('pages/showBooking.html.twig',[
            'current_menu' => 'showBooking',
            'computers' =>$computers
        ]);

    }



}