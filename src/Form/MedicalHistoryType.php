<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MedicalHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('allergies', TextareaType::class, [
                'label' => 'Allergies',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez vos allergies médicamenteuses ou alimentaires',
                    'rows' => 3,
                ],
                'mapped' => false,
            ])
            ->add('antecedentsMedicaux', TextareaType::class, [
                'label' => 'Antécédents médicaux',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Listez vos antécédents médicaux importants',
                    'rows' => 3,
                ],
                'mapped' => false,
            ])
            ->add('antecedentsChirurgicaux', TextareaType::class, [
                'label' => 'Antécédents chirurgicaux',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Listez vos interventions chirurgicales avec leurs dates si possible',
                    'rows' => 3,
                ],
                'mapped' => false,
            ])
            ->add('maladiesChroniques', ChoiceType::class, [
                'label' => 'Maladies chroniques',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Diabète' => 'diabetes',
                    'Hypertension' => 'hypertension',
                    'Asthme' => 'asthma',
                    'Maladie cardiaque' => 'heart_disease',
                    'Maladie rénale' => 'kidney_disease',
                    'Autre' => 'other',
                ],
                'mapped' => false,
            ])
            ->add('autresMaladiesChroniques', TextareaType::class, [
                'label' => 'Autres maladies chroniques',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Précisez vos autres maladies chroniques',
                    'rows' => 2,
                ],
                'mapped' => false,
            ])
            ->add('traitementActuel', TextareaType::class, [
                'label' => 'Traitement actuel',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Listez les médicaments que vous prenez actuellement avec leur posologie',
                    'rows' => 3,
                ],
                'mapped' => false,
            ])
            ->add('antecedentsFamiliaux', TextareaType::class, [
                'label' => 'Antécédents familiaux',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Informations sur les maladies présentes dans votre famille',
                    'rows' => 3,
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
} 