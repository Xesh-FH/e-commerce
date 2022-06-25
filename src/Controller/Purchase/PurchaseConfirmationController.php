<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController
{

    protected FormFactoryInterface $formfactory;
    protected RouterInterface $router;
    protected Security $security;
    protected CartService $cartService;
    protected EntityManagerInterface $em;

    public function __construct(FormFactoryInterface $formfactory, RouterInterface $router, Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->formfactory = $formfactory;
        $this->router = $router;
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        // 1. Lire les données du formulaire
        $form = $this->formfactory->create(CartConfirmationType::class);
        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : on dégage d'ici !
        if (!$form->isSubmitted()) {
            $flashBag->add('warning', [
                'title' => "Commande invalide :",
                'content' => "Le formulaire de confirmation doit être rempli pour valider une commande."
            ]);
            return new RedirectResponse($this->router->generate('cart_show'));
        }

        // 3. Si je ne suis pas connecté : on dégage d'ici !
        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez vous identifier pour passer commande.');
        }

        // 4. Si le panier est vide : on dégage aussi !
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) <= 0) {
            $flashBag->add(
                'warning',
                [
                    'title' => "Panier vide.",
                    'content' => "Vous ne pouvez pas valider une commande d'un panier vide."
                ]
            );
            return new RedirectResponse($this->router->generate('cart_show'));
        }

        // 5. Création d'une Purchase
        // On récupère les données soumises via le formulaire
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. On lie la Purchase à l'utilisateur connecté
        $purchase
            ->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getCartTotal());
        $this->em->persist($purchase);

        // 7. On la lie avec les produits dans le panier
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setProductPrice($cartItem->product->getPrice())
                ->setTotal($cartItem->getTotal());
            $this->em->persist($purchaseItem);
        }

        // 8. On enregistre la commande
        $this->em->flush();

        $flashBag->add('success', [
            'title' => 'Commande enregistrée !',
            'content' => 'Bravo ! Votre commande a bien été prise en compte.'
        ]);
        return new RedirectResponse($this->router->generate('purchase_index'));
    }
}
