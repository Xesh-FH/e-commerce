<?php

namespace App\Event;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ProductViewEvent extends Event
{
    protected Product $product;
    protected ?User $user;

    public function __construct(Product $product, ?User $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
