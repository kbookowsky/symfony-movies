<?php

namespace App\Form;

use App\Entity\Actor;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MovieFormType extends AbstractType
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('Enter Title')
                ),
                'label' => $this->translator->trans('Title'),
                'required' => false,
            ])
            ->add('releaseYear', IntegerType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('Enter Release Year')
                ),
                'label' => $this->translator->trans('Release Year'),
                'required' => false
            ])
            ->add('duration', TextType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('e.g. 1:30')
                ),
                'label' => $this->translator->trans('Duration'),
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none',
                    'placeholder' => $this->translator->trans('Enter Description')
                ),
                'label' => $this->translator->trans('Description'),
                'required' => false
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('Poster'),
                'attr' => [
                    'class' => 'hidden',
                    'data-image' => $builder->getData()->getImagePath(),
                ]
            ])
            ->add('genres', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    $this->translator->trans('Action') => 'action',
                    $this->translator->trans('Comedy') => 'comedy',
                ],
                'label' => $this->translator->trans('Genres'),
                'label_attr' => array(
                    'class' => 'block mb-3'
                ),
                'required' => false
            ])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => $this->translator->trans('Actors'),
                'label_attr' => array(
                    'class' => 'block mb-3'
                ),
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => 'flex'
                ],
                'label' => $this->translator->trans('Save Movie'),
                'attr' => [
                    'class'=> 'uppercase mt-15 bg-blue-900 text-white py-3 px-6 rounded-lg w-fit ml-auto'
                ]
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
