<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $loggerInterface, MailerInterface $mailerInterface)
    {
        $this->logger = $loggerInterface;
        $this->mailer = $mailerInterface;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail',
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        /** @var User */
        $user = $purchaseSuccessEvent->getPurchase()->getUser();
        $purchase = $purchaseSuccessEvent->getPurchase();

        $email = new TemplatedEmail();
        $email
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->from("contact@symshop.com")
            ->subject("Confirmation de votre commande n° {$purchase->getId()}")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $user,
            ]);

        $this->mailer->send($email);

        $this->logger->info(
            "Email envoyé pour la commande n° " .
                $purchaseSuccessEvent->getPurchase()->getId() .
                " à l'adresse : " .
                $purchaseSuccessEvent->getPurchase()->getUser()->getEmail()
        );
    }
}
