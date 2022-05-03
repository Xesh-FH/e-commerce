<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EntityManagerInterface $em)
    {
        $productRepo = $em->getRepository(Product::class);

        $product = $productRepo->find(3);

        return $this->render('home.html.twig');
    }
}
