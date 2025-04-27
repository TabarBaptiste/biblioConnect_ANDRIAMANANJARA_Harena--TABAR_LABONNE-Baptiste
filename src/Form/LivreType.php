<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

//* Entité
use App\Entity\Categorie;
use App\Entity\Langue;
use App\Entity\Auteur;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => 'nom',
                'empty_data' => null,
                'placeholder' => 'Choisir un auteur',
            ])
            ->add('theme')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'empty_data' => null,
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('langue', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'nom',
                'empty_data' => null,
                'placeholder' => 'Choisir une langue',
            ])
            ->add('stock', null, [
                'attr' => ['min' => 0]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
