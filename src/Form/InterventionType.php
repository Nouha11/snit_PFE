<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Equipement;
use App\Entity\Intervention;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeIntervention', ChoiceType::class, [
                'label' => 'type Intervention',
                'choices' => [
                    'curative' => 'curative',
                    'preventive' => 'preventive',
                    'contractuelle' => 'contractuelle',
                    'quotidienne' => 'quotidienne',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('dateIntervention', null, [
                'widget' => 'single_text',
            ])
            ->add('description')
            ->add('equipement', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'modele',
            ])
            ->add('contrat', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'numContrat',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
