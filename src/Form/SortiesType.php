<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('dateDebut', null, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('dateClotureInscription', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre d\'inscriptions',
            ])
            ->add('descriptionInfos', TextType::class, [
                'label' => 'Description',
            ])
            ->add('urlPhoto', TextType::class, [
                'label' => 'Lien de l\'image',
            ])
            ->add('noLieu', EntityType::class, [
                'class' => Lieux::class,
                'choice_label' => 'nomLieu',
                'label' => 'Lieu',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
