<?php

namespace App\Controller;

use App\Entity\Computer;
use App\Form\ComputerType;
use App\Repository\ComputerRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComputerController extends AbstractController
{


    /**
     * @Route ("/computers", name="computer.index")
     * @param ComputerRepository $repository
     * @return Response
     */
    public function index(ComputerRepository $repository):Response
    {
        $computers = $repository->findAll();
        return $this->render('pages/Computer/index.html.twig',[
            'computers' =>$computers,
            'current_menu' => 'computer']);

    }

    /**
     * @Route ("/computer/create", name="computer.create")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request) : Response
    {
        $computer = new Computer();
        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($computer);
            $this->addFlash('success','Ordinateur crée avec succès !');
            $entityManager->flush();

            return $this->redirectToRoute('computer.index');
        }
        return $this->render('pages/Computer/new.html.twig', [
            'computer'=> $computer,
            'form'=> $form->createView(),
            'current_menu' => 'computer']);
    }

    /**
     * @Route ("/computers/{id}", name="computer.edit")
     * @param Computer $computer
     * @param Request $request
     * @return Response
     */
    public function edit(Computer $computer, Request $request){
        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success','Votre modification à bien été enregistré !');
            return $this->redirectToRoute('computer.index',[
                'current_menu' => 'computer']);
        }
        return $this->render('pages/Computer/edit.html.twig', [
            'computer'=> $computer,
            'form'=> $form->createView(),
            'current_menu' => 'computer']);
    }

    /**
     * @Route ("/computers/delete/{id}", name="computer.delete")
     * @param Computer $computer
     * @return Response
     */
    public function delete(Computer $computer, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete'.$computer->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($computer);
            $em->flush();
            $this->addFlash('success','Ordinateur supprimé avec succès !');
            return $this->redirectToRoute('computer.index',[
                'current_menu' => 'computer']);
        }

        return $this->redirectToRoute('computer.index',[
            'current_menu' => 'computer']);
    }
}