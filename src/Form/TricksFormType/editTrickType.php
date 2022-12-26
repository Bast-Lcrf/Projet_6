<?php 

namespace App\Form\TricksFormType;

use App\Entity\Category;
use App\Entity\Tricks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EditTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50',
                ],
                'label' => 'Nom de la figure',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '6'
                ],
                'label' => 'Description du tricks',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ] 
            ])
            ->add('idCategory', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie du tricks',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('images', FileType::class, [
                'label' => 'Choisissez une ou plusieurs images pour le tricks (optionnel) :',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('video', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'help' => 'Youtube UNIQUEMENT - Copier / coller l\'adresse de la video ! (le lien url)',
                'label' => 'Ajoutez une video au tricks (optionnel) :',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Éditer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}