<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function helloWorld(string $prenom)
    {
        return new Response("Hello " . $prenom);
    }
}
