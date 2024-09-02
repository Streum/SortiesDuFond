<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sites;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('noLieu', EntityType::class, [
                'label' => 'Site',
                'class' => Sites::class,
                'choice_label' => 'nomSite',
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Journée plage',
                ]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Entre le ',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => ' et ',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('orga', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e.',
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e.',
                'required' => false,
            ])
            ->add('passee', CheckboxType::class, [
                'label' => 'Sorties passée',
                'required' => false,
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-primary ms-2',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
        ]);
    }
}
