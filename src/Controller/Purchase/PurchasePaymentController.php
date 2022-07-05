<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showPaymentCardForm(int $id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
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

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}
