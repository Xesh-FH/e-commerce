<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
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

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements= {"id":"\d+"})
     * @param int $id l'id du Product à ajouter au Cart
     */
    public function add(int $id, ProductRepository $productRepository, CartService $cartService, Request $request): Response
    {
        // 0. Sécurisation : est-ce que le produit existe ?
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe.");
        }

        $cartService->add($id);

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_remove_item", requirements={"id": "\d+"})
     */
    public function removeItem(int $id, ProductRepository $productRepository, CartService $cartService)
    {
        /** @var Product */
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe, on ne peut donc pas le supprimer.");
        }
        $cartService->remove($id);

        $this->addFlash("success", [
            'title' => 'Suppression réussie',
            'content' => "Le produit " . $product->getName() . " a été retiré du panier."
        ]);

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement_item", requirements={"id": "\d+"})
     */
    public function decrementCartItem(int $id, CartService $cartService, ProductRepository $productRepository)
    {
        /** @var Product */
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe, on ne peut donc en réduire la quantité.");
        }

        $cartService->decrementItem($id);

        return $this->redirectToRoute('cart_show');
    }
}
