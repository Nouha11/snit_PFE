<?php

namespace App\Form;

use App\Entity\Consommable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsommableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Couleur du consommable']
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Modèle du consommable']
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('designation', TextType::class, [
                'label' => 'Désignation',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Désignation du consommable']
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Bon' => 'Bon',
                    'Usagé' => 'Usagé',
                    'Abîmé' => 'Abîmé',
                    'Défectueux' => 'Défectueux'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('valeur', IntegerType::class, [
                'label' => 'Valeur',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consommable::class,
        ]);
    }
}