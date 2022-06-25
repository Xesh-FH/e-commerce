<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom Complet',
                'attr' => [
                    'placeholder' => 'Nom complet pour la livraison',
                ],
            ])
            ->add('adress', TextareaType::class, [
                'label' => "Adresse complète",
                'attr' => [
                    'placeholder' => "Adresse à laquelle livrer la commande.",
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => "Code postal",
                'attr' => [
                    'placeholder' => 'Code postal pour la livraison.'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholer' => "Ville pour la livraison.",
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
