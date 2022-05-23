<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator)
    {
        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class);

        // setData équivaut à passer $product en second paramètre de createForm()
        $form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On remet à jour le slug au cas ou le nom du produit aurait été changé
            if ($product->getSlug() !== (strtolower($slugger->slug($product->getName())))) {
                $product->setSlug(strtolower($slugger->slug($product->getName())));
            }

            //Pas besoin de $em->persist ici, car on travaille sur un élément déjà en base de données.
            $em->flush($product);

            //Redirect à l'ancienne

            $response = new Response();
            $url = $urlGenerator->generate('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);

            $response->headers->set('Location', $url);
            $response->setStatusCode(302);
            return $response;
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
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }
}
