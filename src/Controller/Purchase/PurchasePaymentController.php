<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     */
    public function showPaymentCardForm(int $id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);

        if (!$purchase) {
            return $this->redirectToRoute('cart_show');
        }

        \Stripe\Stripe::setApiKey('sk_test_51LFNFdAICnQ5vYlt3ZGYoB8YnWi2WjaoZ9wEmygsQ6HKdM9gJpaUYJ4ViCS5arvGOQmYBdjSVsmQZXWSP2Y3MKYT00eGXorTX5');
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur',
        ]);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
        ]);
    }
}
