<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rue' ,TextType::class, [
                'label' => 'Rue',
                'attr' => ['maxlength' => 255],
            ])
            ->add('ville' ,TextType::class, [
                'label' => 'Ville',
                'attr' => ['maxlength' => 50],
            ])
            ->add('codePostal',TextType::class, [
                'label' => 'Code Postal',
                'attr' => ['maxlength' => 5, 'minlength' => 3, 'pattern' => '[0-9]{5}'],
            ])
            ->add('pays', ChoiceType::class, [
                'label' => 'Pays',
                'choices' => [
                    'France' => 'France',
                    'Tunisie' => 'Tunisie', ],
            ])
            ->add('complement' ,TextType::class, [
                'label' => 'Complément d\'adresse',
                'attr' => ['maxlength' => 25],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
