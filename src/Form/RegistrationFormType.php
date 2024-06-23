<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Validator\Constraints\Regex;
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, [
            'constraints' => [
                new Email(['message' => 'The email "{{ value }}" is not a valid email.']),
            ],
        ])            ->add('nom')
            ->add('n_telephone', null, [
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'The phone number should be at least {{ limit }} characters long.',
                        // You can add 'max' constraint if needed
                    ]),
                    new Regex([
                        'pattern' => '/^\d{8,}$/',
                        'message' => 'The phone number should contain only digits and be at least 8 characters long.',
                    ]),
                ],
            ])            ->add('prenom')
            ->add('date_de_naissance', DateType::class, [
                'widget' => 'single_text',
                'html5' => false, 
                'format' => 'dd/MM/yyyy', // 'yyyy-MM-dd
                'attr' => [
                    'class' => 'form-control custom-date', 
                    'placeholder' => 'JJ/MM/AAAA', 
                ],
            ])       ;

            if ($options['include_terms']) {
                $builder->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'You should agree to our terms.',
                        ]),
                    ],
                ]);
            }
        
            $builder->add('photo', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false,
                'required' => false,
            ]);
            if ($options['include_password']) {
                $builder->add('plainPassword', PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                    ],
                ]);
            }
    }

    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'include_password' => true,
            'include_terms' => true,
        ]);
    }
}
