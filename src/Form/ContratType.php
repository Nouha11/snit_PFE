<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Equipement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('numContrat')
        ->add('reference')
        ->add('dateEnregistrement')
        ->add('numEnregistrement')
        ->add('equipements', EntityType::class, [
            'class' => Equipement::class,
            'choice_label' => 'modele', 
            'multiple' => true,
            'expanded' => true, 
            'by_reference' => false,
            'label' => 'Équipements',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
