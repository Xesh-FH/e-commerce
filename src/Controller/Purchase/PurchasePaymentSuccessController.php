<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Envent\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/validation/{id}", name="purchase_payment_success")
     * IsGranted("ROLE_USER")
     */
    public function paymentSuccess(
        int $id,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $em,
        CartService $cartService,
        EventDispatcherInterface $dispatcher
    ) {
        $purchase = $purchaseRepository->find($id);

        if (!$purchase) {
            $this->addFlash('warning', [
                'title' => "Commande erronnée.",
                'content' => "Cette commande n'existe pas.",
            ]);
            return $this->redirectToRoute("purchase_index");
        }
        if ($purchase->getUser() !== $this->getUser()) {
            $this->addFlash('warning', [
                'title' => "Commande erronnée.",
                'content' => "Cette commande n'est pas liée à ce compte.",
            ]);
            return $this->redirectToRoute("purchase_index");
        }
        if ($purchase->getStatus() === Purchase::STATUS_PAYED) {
            $this->addFlash('warning', [
                'title' => "Commande erronnée.",
                'content' => "Cette commande a déjà été payée.",
            ]);
            return $this->redirectToRoute("purchase_index");
        }
        $purchase->setStatus(Purchase::STATUS_PAYED);
        $em->flush();

        $cartService->emptyCart();

        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        $this->addFlash('success', [
            'title' => "Paiment effectué.",
            'content' => "La commande a été payée et confirmée.",
        ]);

        return $this->redirectToRoute("purchase_index");
    }
}
