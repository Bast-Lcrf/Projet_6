<?php

namespace App\Form\SecurityFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nouveau mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ],
            'second_options' => [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Confirmation du mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ],
            'invalid_message' => 'Les mots de passe ne correspondent pas.'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
