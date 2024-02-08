<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileFormType extends AbstractType
{

    public function __construct(private Security $security, private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('Name'),
                'required' => false,
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('Email'),
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none'
                ]
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('Profile Image'),
                'attr' => [
                    'class' => 'hidden',
                    'data-image' => $this->security->getUser()->getImagePath()
                ]
            ])
            ->add('bio', TextareaType::class, [
                'label' => $this->translator->trans('Bio'),
                'required' => false,
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none'
                ]
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => 'flex'
                ],
                'label' => $this->translator->trans('Save Changes'),
                'attr' => [
                    'class'=> 'uppercase mt-15 bg-blue-900 text-white py-3 px-6 rounded-lg w-fit ml-auto'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => array(
                'class' => 'flex flex-col gap-y-10'
            )
        ]);
    }
}
