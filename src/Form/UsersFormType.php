<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, ['label' => 'Nom'])
        ->add('prenom', TextType::class, ['label' => 'Prénom'])
        ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
        ->add('email', EmailType::class, ['label' => 'Email'])
        ->add('roles', ChoiceType::class, ['label' => 'Rôles',  'choices'  => [
            'Collaborateur' => "ROLE_USER",
            'Admin' => "ROLE_ADMIN",
        ], "multiple" => 'false'])
        ->add('envoie', SubmitType::class, [
            'label' => 'ajouter', 'attr' => ['class' => 'btn btn-primary mt-3 allWaButton']
        ])
        ;

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
