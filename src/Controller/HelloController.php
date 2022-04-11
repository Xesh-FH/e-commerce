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

    public function __construct(LoggerInterface $logger, Calculator $calculator)
    {
        $this->logger = $logger;
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{prenom}", name="hello", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function helloWorld(string $prenom = "World", Environment $twig)
    {
        $html = $twig->render(
            'hello.html.twig',
            [
                "prenom" => $prenom,
                "formateurs" => [
                    [
                        "prenom" => "Lior",
                        "nom" => "Chamla"
                    ],
                    [
                        "prenom" => "Jérôme",
                        "nom" => "Durand"
                    ]
                ]
            ]
        );
        return new Response($html);
    }
}
