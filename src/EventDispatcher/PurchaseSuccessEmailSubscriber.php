<?php

namespace App\EventDispatcher;

use App\Envent\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $loggerInterface)
    {
        $this->logger = $loggerInterface;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail',
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        $this->logger->info(
            "Email envoyé pour la commande n° " .
                $purchaseSuccessEvent->getPurchase()->getId() .
                " à l'adresse : " .
                $purchaseSuccessEvent->getPurchase()->getUser()->getEmail()
        );
    }
}
