<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserCreationFormType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UsersRepository $usersRepository): Response
    {
 
        $users = $usersRepository->findAll();

       if(count($users) === 0 ){
        return $this->redirectToRoute('app_create_first_user');
       } 

        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_accueil');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'error' => $error,
             'users' => $users,
    
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
