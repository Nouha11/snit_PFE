<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Direction;
use App\Entity\Equipement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pren', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('mobile', TelType::class, [
                'label' => 'Mobile',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'empty_data' => '',
                'help' => 'Laissez vide pour conserver le mot de passe actuel!'
            ])
            ->add('n_bur', IntegerType::class, [
                'label' => 'Numéro de bureau',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('direction', EntityType::class, [
                'class' => Direction::class,
                'choice_label' => 'libelle',
                'label' => 'Direction',
                'placeholder' => 'Choisir une direction',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('equipements', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => function ($equipement) {
                    return $equipement->getNserie() . ' - ' . $equipement->getModele() ;  
                },
                'label' => 'Équipements disponibles',
                'multiple' => true,  
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Aucun équipement disponible',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.utilisateur IS NULL');
                },
                'attr' => ['class' => 'form-control'],
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}