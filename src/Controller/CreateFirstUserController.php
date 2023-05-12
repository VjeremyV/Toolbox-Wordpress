<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserCreationFormType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateFirstUserController extends AbstractController
{
    #[Route('/create/first/user', name: 'app_create_first_user')]
    public function index(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new Users();
        $form = $this->createForm(UserCreationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $pwd = $passwordHasher->hashPassword($user, $user->getPassword()); // pour hasher le pwd
            $user->setPassword($pwd);
            $user->setRoles(['ROLE_ADMIN']);
            $usersRepository->save($user, true);
        }
        $users = $usersRepository->findAll();
        if(count($users) > 0){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('create_first_user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
