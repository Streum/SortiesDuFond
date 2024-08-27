<?php

namespace App\Form;

use App\Entity\Participant;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                    'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('pseudo', PasswordType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('nom', TextType::class, [
            'attr' => ['class' => 'form-control']
                ])
            ->add('prenom', TextType::class, [
                    'attr' => ['class' => 'form-control']
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'agrée les conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez agréer les conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
