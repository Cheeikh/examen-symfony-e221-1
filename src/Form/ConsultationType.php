<?php

namespace App\Form;

use App\Entity\Medecin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de consultation',
                'choices' => [
                    'Consultation générale' => 'generale',
                    'Consultation spécialisée' => 'specialisee',
                    'Consultation de suivi' => 'suivi',
                    'Consultation d\'urgence' => 'urgence',
                ],
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type de consultation',
                    ]),
                ],
            ])
            ->add('service', ChoiceType::class, [
                'label' => 'Service',
                'choices' => [
                    'Médecine générale' => 'Médecine générale',
                    'Cardiologie' => 'Cardiologie',
                    'Pédiatrie' => 'Pédiatrie',
                    'Gynécologie' => 'Gynécologie',
                    'Ophtalmologie' => 'Ophtalmologie',
                    'Dermatologie' => 'Dermatologie',
                    'Radiologie' => 'Radiologie',
                    'Laboratoire' => 'Laboratoire',
                    'Chirurgie' => 'Chirurgie'
                ],
                'placeholder' => 'Sélectionnez un service',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un service',
                    ]),
                ],
            ])
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => function ($medecin) {
                    return 'Dr. ' . $medecin->getNom() . ' ' . $medecin->getPrenom() . ' (' . $medecin->getSpecialite() . ')';
                },
                'placeholder' => 'Choisissez un médecin (optionnel)',
                'required' => false,
                'group_by' => function($medecin) {
                    return $medecin->getSpecialite();
                },
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif de la consultation',
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Veuillez décrire brièvement la raison de votre consultation...'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer le motif de votre consultation',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le motif doit contenir au moins {{ limit }} caractères',
                        'max' => 500,
                        'maxMessage' => 'Le motif ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date souhaitée',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'max' => (new \DateTime('+3 months'))->format('Y-m-d'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une date',
                    ]),
                ],
            ])
            ->add('heure', ChoiceType::class, [
                'label' => 'Heure souhaitée',
                'choices' => $this->getHeureChoices(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une heure',
                    ]),
                ],
            ])
            ->add('symptomes', TextareaType::class, [
                'label' => 'Symptômes',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez vos symptômes de façon détaillée pour aider le médecin (optionnel)...'
                ],
            ])
            ->add('urgence', ChoiceType::class, [
                'label' => 'Niveau d\'urgence',
                'choices' => [
                    'Normal' => 'normal',
                    'Modéré' => 'modere',
                    'Élevé' => 'eleve',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Sélectionnez un niveau d\'urgence (optionnel)',
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaires additionnels',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Informations supplémentaires (optionnel)...'
                ],
            ])
        ;
    }

    private function getHeureChoices(): array
    {
        $choices = [];
        $startHour = 8;
        $endHour = 17;
        
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            $h = str_pad($hour, 2, '0', STR_PAD_LEFT);
            
            // Ajouter l'heure pleine
            $time = $h . ':00';
            $choices[$time] = $time;
            
            // Ajouter la demi-heure sauf pour la dernière heure
            if ($hour < $endHour) {
                $time = $h . ':30';
                $choices[$time] = $time;
            }
        }
        
        return $choices;
    }
} 