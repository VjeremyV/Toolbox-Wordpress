<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersFormType;
use App\Form\UsersUpdateFormType;
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
        $user = new Users();
        $update_user = new Users();
        $form = $this->createForm(UsersFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('info', 'L\'utilisateur a bien été ajouté');
            $pwd = $passwordHasher->hashPassword($user, $user->getPassword()); // pour hasher le pwd
            $user->setPassword($pwd);
            $usersRepository->save($user, true);
        }

        $update_form = $this->createForm(UsersUpdateFormType::class, $update_user);
        $update_form->handleRequest($request);
        $user_in_bdd = "";
                if ($update_form->isSubmitted() && $update_form->isValid()) {
            $user_in_bdd = $usersRepository->findBy(['id' => $update_form['id']->getData()]);
            if(self::isDifferent($update_form['nom']->getData(), $user_in_bdd[0]->getNom())){
                $user_in_bdd[0]->setNom($update_form['nom']->getData());
            }
            $this->addFlash('info', 'L\'utilisateur a bien été modifié');
            // $pwd = $passwordHasher->hashPassword($update_user, $update_user->getPassword()); // pour hasher le pwd
            // $update_user->setPassword($pwd);
            $usersRepository->save($user_in_bdd[0], true);
        }
        $users = $usersRepository->findAll();
        return $this->render(
            'administration/administration.html.twig',
            [
                'users' => $users,
                'form' => $form->createView(),
                'update_form' => $update_form->createView(),
                'role' => $role,
                'userUpdate' => $user_in_bdd[0],
                'one' => $update_form['nom']->getData(),
                'two' =>$user_in_bdd[0]->getNom()
            ]
        );
    }




    #[Route('/administration/supp/{id}', name: 'app_admin_supp')]
    public function admin_supp(UsersRepository $usersRepository, Users $user): Response
    {
        $usersRepository->remove($user, true);
   
        $this->addFlash('info', 'L\'utilisateur a bien été supprimé');
        return $this->redirectToRoute('app_administration');
    }

    private function isDifferent(mixed $value, mixed $compare){
        if($value != $compare){
            return true;
        }
        return false;
    }
}
