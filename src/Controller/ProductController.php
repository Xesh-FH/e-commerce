<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function create(FormFactoryInterface $factory, Request $request)
    {
        $builder = $factory->createBuilder();

        $builder
            ->add('name', TypeTextType::class, [
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

            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Dans quelle catégorie entre votre produit ? --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                },
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $product = new Product();
            $product
                ->setName($data['name'])
                ->setShortDescription($data['shortDescription'])
                ->setPrice($data['price'])
                ->setCategory($data['category']);

            dd($product);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }
}
