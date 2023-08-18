<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    "label" => "Mot de passe",
                    "label_attr" => [
                        "class" => "form-label mt-4"
                    ],
                    "attr" => [
                        "class" => "form-control"
                    ]
                ],
                "second_options" => [
                    "label" => "Confirmation du mot de passe",
                    "label_attr" => [
                        "class" => "form-label mt-4"
                    ],
                    "attr" => [
                        "class" => "form-control"
                    ]
                ],
                'invalid_message' => "les mots de passe ne correspondent pas"
            ])
            ->add('newPassword', PasswordType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                "label" => "Nouveau de passe",
                "label_attr" => [
                    "class" => "form-label mt-4"
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'modifier mon mot de passe'
            ]);;
    }
}
