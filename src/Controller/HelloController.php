<?php

namespace App\Controller;

use App\Taxes\Calculator;
use App\Taxes\Detector;
use Cocur\Slugify\Slugify;
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
     * @Route("/hello/{prenom}", name="hello", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function helloWorld(string $prenom = "World", Slugify $slugify, Detector $detector)
    {
        dump($detector->detect(250));
        dump($detector->detect(10));
        dump($detector->detect(100.001));
        dump($slugify->slugify("Hello World"));
        $this->logger->info("Message de log pour tester le loggerInterface");
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello " . $prenom);
    }
}
