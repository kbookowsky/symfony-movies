<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => $this->translator->trans('Email'),
                'attr' => [
                    'autocomplete' => 'email',                     
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('Email')
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => $this->translator->trans('You should agree to our terms.'),
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $this->translator->trans('Password'),
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',                     
                    'class' => 'bg-white block mt-4 p-2 border border-gray-500 rounded-lg w-full outline-none',
                    'placeholder' => $this->translator->trans('Password')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please enter a password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('Your password should be at least {{ limit }} characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => 'flex'
                ],
                'label' => $this->translator->trans('Register'),
                'attr' => [
                    'class'=> 'uppercase mt-15 bg-blue-900 text-white py-3 px-6 rounded-lg w-fit mx-auto'
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