<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{

    protected Calculator $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/test", name="index")
     */
    public function index()
    {
        $tva = $this->calculator->calcul(200);
        dump($tva);
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
