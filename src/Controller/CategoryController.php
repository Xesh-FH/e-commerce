<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create", methods={"GET", "POST"})
     */
    public function create(): Response
    {
        return $this->render('category/create-category.html.twig', []);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function edit($id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);
        return $this->render('category/edit-category.html.twig', [
            'category' => $category,
        ]);
    }
}
