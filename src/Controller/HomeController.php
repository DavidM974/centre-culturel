<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{


    /**
     * @Route ("/", name="home")
     * @return Response
     */
    public function index():Response
    {
        $dtNow = new \DateTime();
        return $this->render('pages/home.html.twig',[
            'current_menu' => 'home',
            'current_time'=> $dtNow->format('d:m:Y H:i')
        ]);
    }
}