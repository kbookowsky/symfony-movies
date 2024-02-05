<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none'
                ]
            ])
            ->add('imagePath', FileType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none'
                ]
            ])
            ->add('bio', TextareaType::class, [
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
