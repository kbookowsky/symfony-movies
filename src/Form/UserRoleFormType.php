<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRoleFormType extends AbstractType
{

    public function __construct(private Security $security, private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    $this->translator->trans('USER') => 'ROLE_USER',
                    $this->translator->trans('EDITOR') => 'ROLE_EDITOR',
                    $this->translator->trans('ADMIN') => 'ROLE_ADMIN',
                ],
                'choice_attr' => [
                    $this->translator->trans('USER') => ['class' => 'mr-1'],
                    $this->translator->trans('EDITOR') => ['class' => 'ml-10 mr-1'],
                    $this->translator->trans('ADMIN') => ['class' => 'ml-10 mr-1'],
                ],
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
