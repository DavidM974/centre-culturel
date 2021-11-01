<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{


    /**
     * @Route ("/admin/users", name="user.index")
     * @param UserRepository $repository
     * @return Response
     */
    public function index(UserRepository $repository):Response
    {

        $users = $repository->findAll();
        return $this->render('pages/User/index.html.twig',[
            'current_menu' => 'user',
            'users' =>$users
        ]);

    }

    /**
     * @Route ("/admin/users/admin", name="user.admin")
     * @return Response
     */
    public function admin():Response
    {
        return $this->render('pages/User/admin.html.twig',[
            'current_menu' => 'user'
        ]);

    }



    /**
     * @Route ("/admin/user/create", name="user.create")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $this->addFlash('success','Utilisateur crée avec succès !');
            $entityManager->flush();

            return $this->redirectToRoute('user.index');
        }
        return $this->render('pages/User/new.html.twig', [
            'user'=> $user,
            'form'=> $form->createView(),
            'current_menu' => 'user']);
    }

    /**
     * @Route ("/admin/user/createredirect", name="user.create.redirect")
     * @param Request $request
     * @return Response
     */
    public function newRedirect(Request $request) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $this->addFlash('success','Utilisateur crée avec succès !');
            $entityManager->flush();

            return $this->redirectToRoute('booking.create');
        }
        return $this->render('pages/User/new.html.twig', [
            'user'=> $user,
            'form'=> $form->createView(),
            'current_menu' => 'user']);
    }

    /**
     * @Route ("/admin/user/{id}", name="user.edit")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request){
        $form = $this->createForm(userType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success','Votre modification à bien été enregistré !');
            return $this->redirectToRoute('user.index');
        }
        return $this->render('pages/User/edit.html.twig', [
            'user'=> $user,
            'form'=> $form->createView(),
            'current_menu' => 'user']);
    }

    /**
     * @Route ("/admin/user/delete/{id}", name="user.delete")
     * @param User $user
     * @return Response
     */
    public function delete(User $user, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete'.$user->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash('success','Utilisateur supprimé avec succès !');
            return $this->redirectToRoute('user.index',
                ['current_menu' => 'user']);
        }

        return $this->redirectToRoute('user.index',
            ['current_menu' => 'user']);
    }
}