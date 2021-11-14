<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => new Length(null, 2, 30),
                'attr'  => ['placeholder' => 'Veuillez saisir votre prénom'],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => new Length(null, 2, 30),
                'attr'  => ['placeholder' => 'Veuillez saisir votre nom'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => new Length(null, 2, 60),
                'attr'  => ['placeholder' => 'Veuillez saisir votre email'],
            ])
            ->add('password', RepeatedType::class, [
                'type'              => PasswordType::class,
                'invalid_message'   => 'Le mot de passe et la confirmation doivent être identique',
                'label'             => 'Mot de passe',
                'required'          => true,
                'first_options'     => [
                    'label' => 'Mot de passe',
                    'attr'  => ['placeholder' => 'Veuillez saisir un mot de passe'],
                ],
                'second_options'    => [
                    'label' => 'Confirmation du mot de passe',
                    'attr'  => ['placeholder' => 'Veuillez confirmer le mot de passe'],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr'  => [
                    'class' => 'btn btn-lg btn-info btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
