<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    protected ProductRepository $productRepository;
    protected CartService $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        return $this->render('cart/cart_index.html.twig', [
            'items' => $detailedCart,
            'cartTotal' => $cartTotal,
            'confirmationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements= {"id":"\d+"})
     * @param int $id l'id du Product à ajouter au Cart
     */
    public function add(int $id, Request $request): Response
    {
        // 0. Sécurisation : est-ce que le produit existe ?
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe.");
        }

        $this->cartService->add($id);

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
    public function removeItem(int $id)
    {
        /** @var Product */
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe, on ne peut donc pas le supprimer.");
        }
        $this->cartService->remove($id);

        $this->addFlash("success", [
            'title' => 'Suppression réussie',
            'content' => "Le produit " . $product->getName() . " a été retiré du panier."
        ]);

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement_item", requirements={"id": "\d+"})
     */
    public function decrementCartItem(int $id)
    {
        /** @var Product */
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe, on ne peut donc en réduire la quantité.");
        }

        $this->cartService->decrementItem($id);

        return $this->redirectToRoute('cart_show');
    }
}
