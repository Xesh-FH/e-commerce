<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private ProductRepository $productRepository;

    const CART_SESSION_KEY = 'cart';

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    /** This function gets the 'cart' from session  */
    protected function getCart(): array
    {
        return $this->session->get(self::CART_SESSION_KEY, []);
    }

    /** This function saves the 'cart' into session  */
    protected function saveCart(array $cart): void
    {
        $this->session->set(self::CART_SESSION_KEY, $cart);
    }

    /**
     * This function empties the cart key in session 
     */
    public function emptyCart()
    {
        $this->saveCart([]);
    }

    /**
     * This function adds a product found by its $id to the 'cart' array in Session.
     * @param int $id Id of the product to add in cart.
     * @return void 
     */
    public function add(int $id): void
    {
        //Le panier $cart aura la forme d'un tableau clé => valeur associant l'id d'un produit à sa quantité dans le panier.

        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. S'il n'existe pas, prendre un tableau vide
        $cart = $this->getCart();

        // 3. voir si le produit $id existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon ajouter le produit $id avec la quantité 1
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;


        // 6. Enregistrer le tableau à jour dans la session
        $this->saveCart($cart);
    }

    /**
     * This function calculate and returns the total amount of the cart.
     * @return int $total
     */
    public function getCartTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            // Si le produit n'existe pas en base, on ne fait rien sur cette itération de boucle, et on passe à la suite.
            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * This function removes an item from the cart in session.
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        // On  met le cart de la session dans une variable, s'il n'existe pas, on a un tableau vide.
        $cart = $this->getCart();
        // on supprime du tableau l'entrée qui a la clé '$id'
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    /**
     * This function decrements 1 from the quantity of an item in the cart.
     */
    public function decrementItem(int $id): void
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }
        // si le produit n'est qu'en 1 exemplaire dans le panier, on le supprime.
        if ((int) $cart[$id] < 2) {
            $this->remove($id);
            return;
        }
        // dans le cas contraire, on décrémente la valeur de la clé $id qui correspond à la quantité pour cet id.
        $cart[$id]--;
        // On met à jour le cart de la session.
        $this->saveCart($cart);
    }

    /**
     * This function agregates data about the products in cart
     * in order to be able to display detailed infos in views.
     * @return array<CartItem> $detailedCart
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            // Si le produit n'existe pas en base, on ne fait rien sur cette itération de boucle, et on passe à la suite.
            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }
}
