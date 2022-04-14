<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class HelloController extends AbstractController
{

    protected LoggerInterface $logger;
    protected $calculator;
    protected $twig;

    public function __construct(LoggerInterface $logger, Calculator $calculator, Environment $twig)
    {
        $this->logger = $logger;
        $this->calculator = $calculator;
        $this->twig = $twig;
    }

    /**
     * @var string $path
     * @var array $args
     */
    protected function renders(string $path, array $args = [])
    {
        $html = $this->twig->render($path, $args);
        return new Response($html);
    }


    /**
     * @Route("/hello/{prenom}", name="hello", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     * @var string $prenom
     * @return Response
     */
    public function helloWorld(string $prenom = "World"): Response
    {
        return $this->renders('hello.html.twig', ["prenom" => $prenom]);
    }

    /**
     * @Route("/example", name="example")
     * @return Response 
     */
    public function example(): Response
    {
        return $this->renders('example.html.twig', ["age" => 39]);
    }
}
