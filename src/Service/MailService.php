<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService
{
    private $mailer;
    private $senderEmail;
    private $senderName;

    public function __construct(MailerInterface $mailer, string $senderEmail, string $senderName)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    public function sendOrderConfirmation(Order $order): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to($order->getCustomerEmail())
            ->subject('Confirmation de votre commande #' . $order->getReference())
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'order' => $order,
            ]);

        $this->mailer->send($email);
    }

    public function sendOrderShipped(Order $order): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to($order->getCustomerEmail())
            ->subject('Votre commande #' . $order->getReference() . ' a été expédiée')
            ->htmlTemplate('emails/order_shipped.html.twig')
            ->context([
                'order' => $order,
            ]);

        $this->mailer->send($email);
    }
}
