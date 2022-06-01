<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/admin/product/{id}/edit", name="product-edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ValidatorInterface $validator)
    {
        $client = [
            'nom' => '',
            'prenom' => 'Emmanuel',
            'voiture' => [
                'marque' => '',
                'couleur' => "Bleue"
            ]
        ];

        /** Collection de contraintes à appliquer à $client */
        $collection = new Collection([
            'nom' => new NotBlank(['message' => 'Le nom ne doit pas être vide']),
            'prenom' => [
                new NotBlank(['message' => 'Le prénom ne doit pas être vide']),
                new Length(['min' => 3, 'minMessage' => 'Le prénom ne doit pas faire moins de 3 caractères'])
            ],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => 'La voiture doit avoir une marque']),
                'couleur' => new NotBlank(['message' => 'La voiture doit avoir une couleur'])
            ])
        ]);

        $resultat =  $validator->validate($client, $collection);
        if ($resultat->count()) {
            dd("Il y a des erreurs : ", $resultat);
        } else {
            dd("Tout va bien");
        }

        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On remet à jour le slug au cas ou le nom du produit aurait été changé
            if ($product->getSlug() !== (strtolower($slugger->slug($product->getName())))) {
                $product->setSlug(strtolower($slugger->slug($product->getName())));
            }

            //Pas besoin de $em->persist ici, car on travaille sur un élément déjà en base de données.
            $em->flush($product);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product-create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }
}
