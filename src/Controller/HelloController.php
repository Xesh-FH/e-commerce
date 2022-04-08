<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{

    protected LoggerInterface $logger;
    protected $calculator;

    public function __construct(LoggerInterface $logger, Calculator $calculator)
    {
        $this->logger = $logger;
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function helloWorld(string $prenom)
    {
        $this->logger->info("Message de log pour tester le loggerInterface");
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello " . $prenom);
    }
}
