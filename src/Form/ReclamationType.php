<?php

namespace App\Form;

use App\Entity\Reclamation;

use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('sujet', TextType::class, [
            'label' => 'Subject',
            'attr' => ['placeholder' => 'Subject'],
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Message',
            'attr' => ['placeholder' => 'Leave a message here', 'style' => 'height: 150px'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
