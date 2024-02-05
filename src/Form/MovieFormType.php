<?php

namespace App\Form;

use App\Entity\Actor;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => 'Enter title'
                ),
                'label' => 'Title',
                'required' => false
            ])
            ->add('releaseYear', IntegerType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => 'Enter release year'
                ),
                'label' => 'Release Year',
                'required' => false
            ])
            ->add('duration', TextType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => 'e.g. 1:30'
                ),
                'label' => 'Duration',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none',
                    'placeholder' => 'Enter description'
                ),
                'label' => 'Description',
                'required' => false
            ])
            ->add('imagePath', FileType::class, [
                'mapped' => false,
                'attr' => array(
                    'class' => 'py-10'
                ),
                'label' => 'Poster',
                'label_attr' => array(
                    'class' => 'block mb-3'
                ),
                'required' => false
            ])
            ->add('genres', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Action' => 'action',
                    'Comedy' => 'comedy',
                ],
                'label_attr' => array(
                    'class' => 'block mb-3'
                ),
            ])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label_attr' => array(
                    'class' => 'block mb-3'
                ),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
            'attr' => array(
                'class' => 'flex flex-col gap-y-10'
            )
        ]);
    }
}
