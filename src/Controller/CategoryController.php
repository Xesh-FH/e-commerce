<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList()
    {
        // On va chercher les catégories dans les BDD
        $categories = $this->categoryRepository->findAll();
        // Renvoyer le rendu html sous forme d'une Response
        return $this->render("category/_menu.html.twig", [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create", methods={"GET", "POST"})
     */
    public function create(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        return $this->render('category/create-category.html.twig', [
            'formView' => $formView,
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute("security_login");
        }

        if (!in_array("ROLE_ADMIN", $user->getRoles())) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits d'accès à cette ressource.");
        }

        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush($category);

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();
        return $this->render('category/edit-category.html.twig', [
            'category' => $category,
            'formView' => $formView,
        ]);
    }
}
