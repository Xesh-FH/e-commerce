<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{MoneyType, TextareaType, TextType as TextType, UrlType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Saisissez le nom du produit à créer',
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Décrivez succintement votre produit de manière assez claire pour le visiteur',
                ]
            ])
            ->add('price', MoneyType::class, [
                'currency' => true,
                'label' => 'Prix de vente du produit',
                'attr' => [
                    'placeholder' => 'Indiquez le prix de vente TTC du produit en Euros',
                ]
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image de présentation du produit',
                'attr' => [
                    'placeholder' => 'Renseignez l\'URL de l\'image',
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Dans quelle catégorie entre votre produit ? --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
