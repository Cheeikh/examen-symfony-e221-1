<?php

namespace App\Form;

use App\Entity\Medecin;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date du rendez-vous',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une date',
                    ]),
                ],
            ])
            ->add('heure', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure du rendez-vous',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une heure',
                    ]),
                ],
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif du rendez-vous',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer le motif de votre rendez-vous',
                    ]),
                ],
            ])
            ->add('isConsultation', CheckboxType::class, [
                'label' => 'Consultation avec un médecin',
                'required' => false,
                'mapped' => true,
            ])
            ->add('isPrestation', CheckboxType::class, [
                'label' => 'Prestation (Analyse, Radio, etc.)',
                'required' => false,
                'mapped' => true,
            ])
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => function (Medecin $medecin) {
                    return $medecin->getNom() . ' ' . $medecin->getPrenom() . ' (' . $medecin->getSpecialite() . ')';
                },
                'label' => 'Médecin (si consultation)',
                'required' => false,
                'placeholder' => 'Choisissez un médecin',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de prestation (si applicable)',
                'required' => false,
                'choices' => [
                    'Choisir un type' => '',
                    'Analyse de sang' => 'Analyse de sang',
                    'Radiographie' => 'Radiographie',
                    'Échographie' => 'Échographie',
                    'Scanner' => 'Scanner',
                    'IRM' => 'IRM',
                    'Autre' => 'Autre',
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
