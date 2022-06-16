<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements= {"id":"\d+"})
     * @param int $id l'id du Product à ajouter au Cart
     */
    public function add(int $id, ProductRepository $productRepository, CartService $cartService): Response
    {
        // 0. Sécurisation : est-ce que le produit existe ?
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe.");
        }

        $cartService->add($id);

        $this->addFlash('success', [
            'title' => "Félicitations",
            'content' => "Produit ajouté au panier.",
        ]);

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {
        $detailedCart = $cartService->getDetailedCartItems();
        $cartTotal = $cartService->getCartTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'cartTotal' => $cartTotal,
        ]);
    }
}
