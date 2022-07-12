<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected SluggerInterface $slugger;
    protected ProductRepository $productRepository;

    public function __construct(SluggerInterface $slugger, ProductRepository $productRepository)
    {
        $this->slugger = $slugger;
        $this->productRepository = $productRepository;
    }
    public function prePersist(Product $entity)
    {
        if (empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
            $increment = 0;
            while ($this->checkSlugInDataBase($entity) > 0) {
                $increment++;
                $entity->setSlug(strtolower($this->slugger->slug($entity->getName())) . "-" . $increment);
            }
        }
    }

    private function checkSlugInDataBase(Product $product)
    {
        return count($this->productRepository->findBy(['slug' => $product->getSlug()]));
    }
}
