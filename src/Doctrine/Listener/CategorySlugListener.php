<?php

namespace App\Doctrine\Listener;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorySlugListener
{
    protected SluggerInterface $slugger;
    protected CategoryRepository $categoryRepository;

    public function __construct(SluggerInterface $sluggerInterface, CategoryRepository $categoryRepository)
    {
        $this->slugger = $sluggerInterface;
        $this->categoryRepository = $categoryRepository;
    }

    public function prePersist(Category $entity)
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

    private function checkSlugInDataBase(Category $category)
    {
        return count($this->categoryRepository->findBy(['slug' => $category->getSlug()]));
    }
}
