<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $requestStack;
    private $entityManager;
    private $session;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->session = $requestStack->getSession();
    }

    public function getCart(?User $user = null): Cart
    {
        $cart = null;
        
        if ($user) {
            $cart = $user->getCart();
            
            if (!$cart) {
                $cart = new Cart();
                $cart->setUser($user);
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
            }
        } else {
            $cartId = $this->session->get('cart_id');
            
            if ($cartId) {
                $cart = $this->entityManager->getRepository(Cart::class)->find($cartId);
            }
            
            if (!$cart) {
                $cart = new Cart();
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
                $this->session->set('cart_id', $cart->getId());
            }
        }
        
        return $cart;
    }

    public function add(Product $product, int $quantity = 1, ?User $user = null): void
    {
        $cart = $this->getCart($user);
        
        // Vérifier si le produit est déjà dans le panier
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $this->entityManager->flush();
                return;
            }
        }
        
        // Créer un nouvel item
        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity($quantity);
        
        $cart->addItem($item);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function remove(CartItem $item, ?User $user = null): void
    {
        $cart = $this->getCart($user);
        $cart->removeItem($item);
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    public function updateQuantity(CartItem $item, int $quantity, ?User $user = null): void
    {
        if ($quantity <= 0) {
            $this->remove($item, $user);
            return;
        }
        
        $item->setQuantity($quantity);
        $this->entityManager->flush();
    }

    public function clear(?User $user = null): void
    {
        $cart = $this->getCart($user);
        $cart->removeItems();
        $this->entityManager->flush();
    }

    public function getTotal(?User $user = null): float
    {
        return $this->getCart($user)->getTotal();
    }

    public function getItemCount(?User $user = null): int
    {
        return count($this->getCart($user)->getItems());
    }
}
