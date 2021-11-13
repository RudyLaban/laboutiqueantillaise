<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom d\'adresse',
                'attr'  => [
                    'placeholder' => 'Saisissez un nom d\'adresse',
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr'  => [
                    'placeholder' => 'Saisissez votre prénom',
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'placeholder' => 'Saisissez votre nom',
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Société',
                'required'    => false,
                'attr'  => [
                    'placeholder' => '(facultatif) Saisissez le nom de votre société',
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr'  => [
                    'placeholder' => '512 Residence Ti Savann\', 6 Chemin de la Chatterie ...',
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code postal',
                'attr'  => [
                    'placeholder' => 'Saisissez votre code postal',
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr'  => [
                    'placeholder' => 'Saisissez le nom de votre ville',
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => [
                    'placeholder' => 'Selectionnez votre pays',
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr'  => [
                    'placeholder' => 'Saisissez votre n° de téléphone',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr'  => [
                    'class' => 'btn-block btn-info' ,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
