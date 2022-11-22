<?php

namespace App\Form;

use App\Entity\Tricks;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormTypeInterface;

class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idCategory')
            ->add('name')
            ->add('description')
            ->add('createAt')
            ->add('updateAt')
        ;
        
        /*
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control rounded-0',
                    'placeholder' => 'Nom de la figure',
                ],
                'constraints' => [
                    new NotNull(message: 'Veuillez ajouter un nom à la figure', groups: ['trick_new', 'trick_edit'])
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control rounded-0',
                    'placeholder' => 'description de la figure',
                ],
                'constraints' => [
                    new NotNull(message: 'Veuillez ajouter un nom à la figure', groups: ['trick_new', 'trick_edit'])
                ]
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
            'validation_groups' => [],
        ]);
    }
}
