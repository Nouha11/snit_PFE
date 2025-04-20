<?php

namespace App\Form;

use App\Entity\Equipement;
use App\Entity\Utilisateur;
use App\Entity\Consommable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Désignation',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Inventaire', TextType::class, [
                'label' => 'Inventaire',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('N_serie', TextType::class, [
                'label' => 'Numéro de série',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('Etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Bon état' => 'Bon état',
                    'État moyen' => 'État moyen',
                    'Mauvais état' => 'Mauvais état',
                    'Hors service' => 'Hors service'
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function ($utilisateur) {
                    return sprintf(
                        'ID: %s | Num Bureau: %s | %s %s | Direction: %s ',
                        $utilisateur->getIdU(),
                        $utilisateur->getNbur(),
                        $utilisateur->getNom(),
                        $utilisateur->getPren(),
                        $utilisateur->getDirection()?->getLibelle(),
                    );
                },

                'placeholder' => 'Sélectionner un utilisateur',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('consommable', EntityType::class, [
                'class' => Consommable::class,
                'choice_label' => function ($consommable) {
                    // Adjust this based on your Consommable entity properties
                    return $consommable->getDesignation() ?? $consommable->getId_cons();
                },
                'placeholder' => 'Sélectionner un consommable',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}