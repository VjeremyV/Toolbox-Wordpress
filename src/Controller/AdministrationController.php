<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersFormType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdministrationController extends AbstractController
{
    #[Route('/administration', name: 'app_administration')]
    public function index(UsersRepository $usersRepository, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $role = "";
        $users = $usersRepository->findAll();
        $user = new Users();
        $form = $this->createForm(UsersFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('info', 'L\'utilisateur a bien été ajouté');
            $pwd = $passwordHasher->hashPassword($user, $user->getPassword()); // pour hasher le pwd
            $user->setPassword($pwd);
            $usersRepository->save($user, true);
        }
        return $this->render(
            'administration/administration.html.twig',
            [
                'users' => $users,
                'form' => $form->createView(),
                'role' => $role
            ]
        );
    }
}
