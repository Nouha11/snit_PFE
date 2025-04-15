<?php

namespace App\Form;

use App\Entity\Consommable;
use App\Entity\Equipement;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('n_bur')
            ->add('direction')
            ->add('designation')
            ->add('Modele')
            ->add('Inventaire')
            ->add('N_serie')
            ->add('Description')
            ->add('Etat')
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
            ->add('consommable', EntityType::class, [
                'class' => Consommable::class,
                'choice_label' => 'id',
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
