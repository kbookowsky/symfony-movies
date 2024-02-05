<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heading', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => 'Review heading'
                ],
            ])
            ->add('review', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                ],
            ])
            ->add('content', TextareaType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none',
                    'placeholder' => 'Enter description'
                ),
                'label' => 'Description',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
