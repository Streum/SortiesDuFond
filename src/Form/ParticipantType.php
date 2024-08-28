<?php

namespace App\Form;

use App\Entity\Inscriptions;
use App\Entity\Participant;
use App\Entity\Sites;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
        'label' => 'Email',
        'attr' => ['class' => 'form-control']
    ])
            ->add('pseudo', TextType::class, [
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
            ->add('site', EntityType::class, [
                'class' => Sites::class,
                'choice_label' => 'nomSite',
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'Actif',
                'expanded' => true,  // Affiche les choix sous forme de boutons radio
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])

            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Organisateur' => 'ROLE_ORGANISATEUR',
                ],
                'multiple' => true, // Permet de sélectionner plusieurs rôles
                'expanded' => true, // Affiche les rôles en checkboxes
            ])
            ->add('sorties', EntityType::class, [
                'class' => Sorties::class,
                'choice_label' => 'nom', // Remplacez par le champ qui représente bien la sortie
                'multiple' => true,
                'expanded' => true,
                'label' => 'Sorties Organisé'
            ])
            ->add('inscriptions', EntityType::class, [
                'class' => Inscriptions::class,
                'choice_label' => function (Inscriptions $inscription) {
                return $inscription->getNoSortie()->getNom(); // Assurez-vous que getNoSortie() et getNom() sont corrects
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Inscriptions'
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
