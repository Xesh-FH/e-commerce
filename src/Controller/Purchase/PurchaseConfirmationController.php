<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PurchaseConfirmationController extends AbstractController
{
    protected CartService $cartService;
    protected EntityManagerInterface $em;
    protected PurchasePersister $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez ête connecté pour confirmer une commande.")
     */
    public function confirm(Request $request)
    {
        // 1. Lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : on dégage d'ici !
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', [
                'title' => "Commande invalide :",
                'content' => "Le formulaire de confirmation doit être rempli pour valider une commande."
            ]);
            return $this->redirectToRoute('cart_show');
        }

        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) <= 0) {
            $this->addFlash(
                'warning',
                [
                    'title' => "Panier vide.",
                    'content' => "Vous ne pouvez pas valider une commande d'un panier vide."
                ]
            );
            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        $this->cartService->emptyCart();

        $this->addFlash('success', [
            'title' => 'Commande enregistrée !',
            'content' => 'Bravo ! Votre commande a bien été prise en compte.'
        ]);
        return $this->redirectToRoute('purchase_index');
    }
}
