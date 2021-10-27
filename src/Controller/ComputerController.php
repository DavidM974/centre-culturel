<?php

namespace App\Controller;

use App\Entity\Computer;
use App\Form\ComputerType;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComputerController extends AbstractController
{


    /**
     * @Route ("/computers", name="computer")
     * @param ComputerRepository $repository
     * @return Response
     */
    public function index(ComputerRepository $repository):Response
    {
        $computers = $repository->findAll();
        return $this->render('pages/Computer/index.html.twig',[
            'current_menu' => 'computers',
            'computers' =>$computers
        ]);

    }
    /**
     * @Route ("/computers/{id}", name="admin.computer.edit")
     */
    public function edit(Computer $computer){
        $form = $this->createForm(ComputerType::class, $computer);
        return $this->render('pages/Computer/edit.html.twig', [
            'computer'=> $computer,
            'form'=> $form->createView()
        ]);
    }
}