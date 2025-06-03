<?php

namespace App\Form;

use App\Entity\Consommable;
use App\Entity\Equipement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('equipements', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => function ($equipement) {
                    return $equipement->getNserie() . ' - ' . $equipement->getModele() ;  
                },
                'label' => 'Équipements',
                'multiple' => true,  
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Aucun équipement disponible',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.consommable IS NULL OR e.consommable = 999');
                },
                'attr' => ['class' => 'form-control'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consommable::class,
        ]);
    }
}