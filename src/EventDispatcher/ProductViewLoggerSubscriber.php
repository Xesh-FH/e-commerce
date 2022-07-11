<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewLoggerSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            "product.view" => "logProductView"
        ];
    }

    public function logProductView(ProductViewEvent $productViewEvent)
    {
        $this->logger->info(
            "Le produit " .
                $productViewEvent->getProduct()->getName() .
                " (" .
                $productViewEvent->getProduct()->getId() .
                ") " .
                "a été consulté par " .
                $productViewEvent->getUser()->getFullName() .
                " (" .
                $productViewEvent->getUser()->getId() .
                ")."
        );
    }
}
