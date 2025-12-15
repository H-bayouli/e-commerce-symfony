<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


final class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_user')]
    public function users(UserRepository $ur): Response
    {
        $users=$ur->findAll();
        return $this->render('user/index.html.twig', ['users'=>$users]);
    }

    #[Route('/admin/{id}/to/editor', name: 'app_user_to_editor')]
    public function changeRole(EntityManagerInterface $em,User $user): Response
    {
        $user->setRoles(["ROLE_EDITOR","ROLE_USER"]);
        $em->flush();

        $this->addFlash('success','le role éditeur a été ajouté à cet utilisateur');

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/{id}/remove/editor', name: 'app_user_remove_editor')]
    public function RemmoveEditorRole(EntityManagerInterface $em,User $user): Response
    {
        $user->setRoles([]);
        $em->flush();

        $this->addFlash('success','le role éditeur a été retiré à cet utilisateur');

        return $this->redirectToRoute('app_user');
    }

    #[Route('admin/{id}', name: 'app_user_delete')]   
    public function delete($id, UserRepository $ur, EntityManagerInterface $em): Response
    {
        $user=$ur->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }
        $em->remove($user);
        $em->flush();
        $this->addFlash('danger','l\'utilisateur a été supprimé');
        return $this->redirectToRoute('app_user');
    }

    /*
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/dashboard.html.twig');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function admindashboard(): Response
    {
        return $this->render('dashboard/admin_dashboard.html.twig');
    }
    */
}
