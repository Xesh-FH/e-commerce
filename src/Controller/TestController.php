<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        dd("ça fonctionne");
    }

    /**
     * @Route("/test/{age<\d+>?0}", name ="test", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function test(int $age)
    {
        return new Response("Vous avez $age an(s)");
    }
}
