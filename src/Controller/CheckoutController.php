<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private $cartService;
    private $entityManager;
    private $paypalClientId;
    private $paypalSecret;
    private $mailService;

    public function __construct(
        CartService $cartService, 
        EntityManagerInterface $entityManager,
        string $paypalClientId,
        string $paypalSecret,
        MailService $mailService
    ) {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
        $this->paypalClientId = $paypalClientId;
        $this->paypalSecret = $paypalSecret;
        $this->mailService = $mailService;
    }

    /**
     * @Route("/commande/recapitulatif", name="checkout")
     */
    public function index(): Response
    {
        $cart = $this->cartService->getCart($this->getUser());
        
        if (count($cart->getItems()) === 0) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('cart_show');
        }

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'total' => $this->cartService->getTotal($this->getUser())
        ]);
    }

    /**
     * @Route("/commande/paiement", name="checkout_payment", methods={"POST"})
     */
    public function payment(): Response
    {
        $cart = $this->cartService->getCart($this->getUser());
        
        if (count($cart->getItems()) === 0) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('cart_show');
        }

        // Création de la commande
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setReference(uniqid('CMD'));
        $order->setStatus('pending');
        $order->setTotal($this->cartService->getTotal($this->getUser()));

        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getProduct()->getPrice());
            $orderItem->setOrderRef($order);
            
            $this->entityManager->persist($orderItem);
            $order->addItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // Configuration de PayPal
        $apiContext = $this->getApiContext();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal(number_format($order->getTotal(), 2, '.', ''));
        $amount->setCurrency('EUR');

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Paiement de la commande #" . $order->getReference());
        $transaction->setInvoiceNumber($order->getReference());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->generateUrl('checkout_success', [], true))
                    ->setCancelUrl($this->generateUrl('checkout_cancel', [], true));

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($apiContext);
            
            // Sauvegarder l'ID de paiement PayPal
            $order->setPaypalPaymentId($payment->getId());
            $this->entityManager->flush();

            return $this->redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création du paiement PayPal: ' . $e->getMessage());
            return $this->redirectToRoute('checkout');
        }
    }

    /**
     * @Route("/commande/success", name="checkout_success")
     */
    public function success(Request $request): Response
    {
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        if (empty($payerId) || empty($paymentId)) {
            $this->addFlash('error', 'Paiement échoué.');
            return $this->redirectToRoute('cart_show');
        }

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['paypalPaymentId' => $paymentId]);
        
        if (!$order) {
            $this->addFlash('error', 'Commande introuvable.');
            return $this->redirectToRoute('home');
        }

        $payment = Payment::get($paymentId, $this->getApiContext());
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->getApiContext());
            
            if ($result->getState() === 'approved') {
                // Mise à jour de la commande
                $order->setStatus('paid');
                $order->setPaypalPayerId($payerId);
                
                // Mettre à jour les stocks
                foreach ($order->getItems() as $item) {
                    $product = $item->getProduct();
                    $product->setStock($product->getStock() - $item->getQuantity());
                    $this->entityManager->persist($product);
                }
                
                $this->entityManager->flush();

                // Envoyer un email de confirmation
                $this->mailService->sendOrderConfirmation($order);

                // Vider le panier
                $this->cartService->clear($this->getUser());

                return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'exécution du paiement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('checkout_cancel');
    }

    /**
     * @Route("/commande/annulation", name="checkout_cancel")
     */
    public function cancel(): Response
    {
        $this->addFlash('warning', 'Paiement annulé. Votre commande a été enregistrée et vous pouvez la retrouver dans votre espace client.');
        return $this->redirectToRoute('account_orders');
    }

    /**
     * @Route("/commande/confirmation/{id}", name="order_confirmation")
     */
    public function confirmation(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order
        ]);
    }

    private function getApiContext(): ApiContext
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->paypalClientId,
                $this->paypalSecret
            )
        );

        $apiContext->setConfig([
            'mode' => 'sandbox', // Changez à 'live' pour la production
            'log.LogEnabled' => true,
            'log.FileName' => '../var/log/paypal.log',
            'log.LogLevel' => 'DEBUG',
            'cache.enabled' => true,
        ]);

        return $apiContext;
    }
}
