<?php

namespace App\Cart;

use App\Entity\Product;

/**
 * This Class represents an item stored in the Cart ($session->get('cart'))
 */
class CartItem
{
    public Product $product;
    public int $qty;

    public function __construct(Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    /**
     * This function calculates and returns the total amount for one item in the cart
     */
    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->qty;
    }
}
