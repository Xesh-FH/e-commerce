<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements= {"id":"\d+"})
     * @param int $id l'id du Product à ajouter au Cart
     */
    public function add(int $id, ProductRepository $productRepository, SessionInterface $session, FlashBagInterface $flashBag): Response
    {
        // 0. Sécurisation : est-ce que le produit existe ?
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Aucun produit avec l'id $id n'existe.");
        }

        //Le panier $cart aura la forme d'un tableau clé => valeur associant l'id d'un produit à sa quantité dans le panier.

        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. S'il n'existe pas, prendre un tableau vide
        $cart = $session->get('cart', []);

        // 3. voir si le produit $id existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon ajouter le produit $id avec la quantité 1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // 6. Enregistrer le tableau à jour dans la session
        $session->set('cart', $cart);

        $flashBag->add('success', "Produit ajouté au panier.");

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);
    }
}
