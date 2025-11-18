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

// Je suis le contrôleur qui gère le processus de commande : récapitulatif, création de la commande
// et intégration du paiement PayPal, ainsi que le traitement du retour (succès/annulation)
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
        // J'injecte le service panier, l'EntityManager, les identifiants PayPal et mon service mail
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
        // Je récupère le panier de l'utilisateur connecté
        $cart = $this->cartService->getCart($this->getUser());
        
        // Si le panier est vide, je renvoie l'utilisateur vers le panier avec un message
        if (count($cart->getItems()) === 0) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('cart_show');
        }

        // Sinon, j'affiche la page de récapitulatif de commande avec le total calculé
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
        // Je récupère le panier de l'utilisateur
        $cart = $this->cartService->getCart($this->getUser());
        
        // Si le panier est vide, je ne lance pas de paiement
        if (count($cart->getItems()) === 0) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('cart_show');
        }

        // Je crée une nouvelle commande en base à partir du panier
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setReference(uniqid('CMD'));
        $order->setStatus('pending');
        $order->setTotal($this->cartService->getTotal($this->getUser()));

        // Pour chaque ligne du panier, je crée un OrderItem lié à la commande
        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getProduct()->getPrice());
            $orderItem->setOrderRef($order);
            
            $this->entityManager->persist($orderItem);
            $order->addItem($orderItem);
        }

        // Je persiste la commande et ses lignes
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // Je prépare le contexte API PayPal
        $apiContext = $this->getApiContext();

        // Je définis le payeur comme étant PayPal
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Je prépare le montant total de la commande pour PayPal
        $amount = new Amount();
        $amount->setTotal(number_format($order->getTotal(), 2, '.', ''));
        $amount->setCurrency('EUR');

        // Je construis la transaction PayPal avec le montant et une description
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Paiement de la commande #" . $order->getReference());
        $transaction->setInvoiceNumber($order->getReference());

        // Je définis les URLs de retour en cas de succès ou d'annulation du paiement
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->generateUrl('checkout_success', [], true))
                    ->setCancelUrl($this->generateUrl('checkout_cancel', [], true));

        // Je crée l'objet Payment PayPal avec toutes les informations
        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            // Je demande à PayPal de créer le paiement
            $payment->create($apiContext);
            
            // Je sauvegarde l'ID de paiement PayPal sur la commande pour le retrouver au retour
            $order->setPaypalPaymentId($payment->getId());
            $this->entityManager->flush();

            // Je redirige l'utilisateur vers la page de confirmation PayPal
            return $this->redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            // En cas d'erreur, j'affiche un message et je renvoie sur le récapitulatif
            $this->addFlash('error', 'Erreur lors de la création du paiement PayPal: ' . $e->getMessage());
            return $this->redirectToRoute('checkout');
        }
    }

    /**
     * @Route("/commande/success", name="checkout_success")
     */
    public function success(Request $request): Response
    {
        // Je récupère les paramètres renvoyés par PayPal
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        // Si des informations manquent, je considère le paiement comme échoué
        if (empty($payerId) || empty($paymentId)) {
            $this->addFlash('error', 'Paiement échoué.');
            return $this->redirectToRoute('cart_show');
        }

        // Je retrouve la commande correspondante grâce à l'ID de paiement PayPal
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['paypalPaymentId' => $paymentId]);
        
        if (!$order) {
            $this->addFlash('error', 'Commande introuvable.');
            return $this->redirectToRoute('home');
        }

        // Je récupère le paiement PayPal pour l'exécuter
        $payment = Payment::get($paymentId, $this->getApiContext());
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            // J'exécute le paiement côté PayPal
            $result = $payment->execute($execution, $this->getApiContext());
            
            if ($result->getState() === 'approved') {
                // Je mets à jour le statut de la commande en "paid" et je sauvegarde l'identifiant du payeur
                $order->setStatus('paid');
                $order->setPaypalPayerId($payerId);
                
                // Je parcours les lignes de commande pour mettre à jour les stocks produits
                foreach ($order->getItems() as $item) {
                    $product = $item->getProduct();
                    $product->setStock($product->getStock() - $item->getQuantity());
                    $this->entityManager->persist($product);
                }
                
                $this->entityManager->flush();

                // J'envoie un email de confirmation de commande au client
                $this->mailService->sendOrderConfirmation($order);

                // Je vide le panier de l'utilisateur puisque la commande est payée
                $this->cartService->clear($this->getUser());

                // Je redirige vers la page de confirmation de commande
                return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
            }
        } catch (\Exception $e) {
            // En cas d'erreur pendant l'exécution du paiement, j'affiche un message
            $this->addFlash('error', 'Erreur lors de l\'exécution du paiement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('checkout_cancel');
    }

    /**
     * @Route("/commande/annulation", name="checkout_cancel")
     */
    public function cancel(): Response
    {
        // Je préviens l'utilisateur que le paiement a été annulé
        $this->addFlash('warning', 'Paiement annulé. Votre commande a été enregistrée et vous pouvez la retrouver dans votre espace client.');
        // Je le redirige vers la page de ses commandes
        return $this->redirectToRoute('account_orders');
    }

    /**
     * @Route("/commande/confirmation/{id}", name="order_confirmation")
     */
    public function confirmation(Order $order): Response
    {
        // Je vérifie que la commande appartient bien à l'utilisateur connecté
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        // J'affiche la page de confirmation avec les détails de la commande
        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order
        ]);
    }

    private function getApiContext(): ApiContext
    {
        // Je construis le contexte API PayPal à partir de mes identifiants
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->paypalClientId,
                $this->paypalSecret
            )
        );

        // Je configure PayPal en mode sandbox (test) avec les logs activés
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

