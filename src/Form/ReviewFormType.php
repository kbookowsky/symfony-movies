<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewFormType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heading', TextType::class, [
                'label' => $this->translator->trans('Title'),
                'attr' => [
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('Enter title')
                ],
            ])
            ->add('review', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    '⭐⭐⭐⭐⭐' => '5',
                    '⭐⭐⭐⭐' => '4',
                    '⭐⭐⭐' => '3',
                    '⭐⭐' => '2',
                    '⭐' => '1'
                ],
                'choice_attr' => [
                    '⭐⭐⭐⭐⭐' => ['class' => 'mr-1'],
                    '⭐⭐⭐⭐' => ['class' => 'ml-10 mr-1'],
                    '⭐⭐⭐' => ['class' => 'ml-10 mr-1'],
                    '⭐⭐' => ['class' => 'ml-10 mr-1'],
                    '⭐' => ['class' => 'ml-10 mr-1']
                ],
            ])
            ->add('content', TextareaType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full h-60 outline-none',
                    'placeholder' => $this->translator->trans('Enter Description')
                ),
                'label' => $this->translator->trans('Description'),
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => 'flex'
                ],
                'label' => $this->translator->trans('Add Review'),
                'attr' => [
                    'class'=> 'uppercase mt-15 bg-blue-900 text-white py-3 px-6 rounded-lg w-fit ml-auto'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            'attr' => array(
                'class' => 'flex flex-col gap-y-10'
            )
        ]);
    }
}
