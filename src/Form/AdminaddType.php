<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Add this line

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminaddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class)
            ->add('photo', FileType::class, [ // Change this line
                'label' => 'Photo', // Set the label for the file input
                'required' => false, // Set to true if the photo field is required
            ])            ->add('n_telephone')
            ->add('nom')
            ->add('prenom')
      
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
