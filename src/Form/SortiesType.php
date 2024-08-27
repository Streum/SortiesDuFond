<?php

namespace App\Form;

use App\Entity\Etats;
use App\Entity\Lieux;
use App\Entity\Participant;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dateCloture', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('nbInscriptionsMax')
            ->add('descriptionInfos')
            ->add('urlPhoto')
            ->add('noEtat', EntityType::class, [
                'class' => Etats::class,
                'choice_label' => 'libelle',
            ])
            ->add('noLieu', EntityType::class, [
                'class' => Lieux::class,
                'choice_label' => 'nomLieu',
            ])
            ->add('noParticipant', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'prenom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
