<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            "slug" => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie $slug n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show(string $slug, ProductRepository $productRepository)
    {
        $product = $productRepository->findOneBy([
            "slug" => $slug,
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $slug n'existe pas");
        }

        return $this->render("product/show.html.twig", [
            "product" => $product,
        ]);
    }


    /**
     * @Route("/admin/product/create", name="product-create")
     */
    public function create(FormFactoryInterface $factory, CategoryRepository $categoryRepository)
    {
        $builder = $factory->createBuilder();

        $builder
            ->add('name', TypeTextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le nom du produit à créer',
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez succintement votre produit de manière assez claire pour le visiteur',
                ]
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'label' => 'Prix de vente du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Indiquez le prix de vente TTC du produit en Euros',
                ]
            ]);

        $options = [];
        foreach ($categoryRepository->findAll() as $category) {
            $options[$category->getName()] = $category->getId();
        }

        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => '-- Dans quelle catégorie entre votre produit ? --',
                'choices' => $options
            ]);

        $form = $builder->getForm();

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }
}
