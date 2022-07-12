<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewLoggerSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            "product.view" => "logProductView"
        ];
    }

    public function logProductView(ProductViewEvent $productViewEvent)
    {
        $email = new Email();
        $email
            ->from(new Address("contact@symshop.com", "Infos de la boutique"))
            ->to("admin@symshop.com")
            ->text("Un visiteur consulte actuellement la page du produit n° {$productViewEvent->getProduct()->getId()}")
            ->subject("Visite du produit {$productViewEvent->getProduct()->getId()}");

        $this->mailer->send($email);


        $this->logger->info(
            "Le produit " .
                $productViewEvent->getProduct()->getName() .
                " (" .
                $productViewEvent->getProduct()->getId() .
                ") " .
                "a été consulté par " .
                $this->buildVisiteurString($productViewEvent->getUser())
        );
    }

    private function buildVisiteurString(?User $user): string
    {
        if (!$user) {
            return " un visiteur annonyme.";
        }
        if ($user) {
            return  $user->getFullName() .
                " (" .
                $user->getId() .
                ").";
        }
    }
}
