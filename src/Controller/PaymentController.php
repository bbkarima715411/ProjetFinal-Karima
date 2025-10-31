<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $paypalClientId;
    private $paypalSecret;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $paypalClientId, string $paypalSecret)
    {
        $this->entityManager = $entityManager;
        $this->paypalClientId = $paypalClientId;
        $this->paypalSecret = $paypalSecret;
    }

    /**
     * @Route("/checkout/paypal/{id}", name="paypal_checkout")
     */
    public function paypalCheckout(Order $order): Response
    {
        $apiContext = $this->getApiContext();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal(number_format($order->getTotal(), 2, '.', ''));
        $amount->setCurrency('EUR');

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Paiement de la commande #" . $order->getReference());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->generateUrl('paypal_success', ['id' => $order->getId()], true))
            ->setCancelUrl($this->generateUrl('paypal_cancel', ['id' => $order->getId()], true));

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
            return $this->redirectToRoute('cart_show');
        }
    }

    /**
     * @Route("/payment/success/{id}", name="paypal_success")
     */
    public function paypalSuccess(Order $order, Request $request): Response
    {
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        if (empty($payerId) || empty($paymentId)) {
            $this->addFlash('error', 'Paiement échoué.');
            return $this->redirectToRoute('cart_show');
        }

        $payment = Payment::get($paymentId, $this->getApiContext());
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->getApiContext());
            
            if ($result->getState() === 'approved') {
                $order->setStatus('paid');
                $order->setPaypalPayerId($payerId);
                $this->entityManager->flush();

                // TODO: Envoyer un email de confirmation
                
                $this->addFlash('success', 'Paiement effectué avec succès !');
                return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'exécution du paiement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/payment/cancel/{id}", name="paypal_cancel")
     */
    public function paypalCancel(Order $order): Response
    {
        $order->setStatus('cancelled');
        $this->entityManager->flush();
        
        $this->addFlash('warning', 'Paiement annulé.');
        return $this->redirectToRoute('cart_show');
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
