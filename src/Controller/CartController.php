<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;
    private $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/panier", name="cart_show")
     */
    public function show(): Response
    {
        $cart = $this->cartService->getCart($this->getUser());
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $this->cartService->getTotal($this->getUser())
        ]);
    }

    /**
     * @Route("/panier/ajouter/{id}", name="cart_add")
     */
    public function add(Product $product, Request $request): Response
    {
        $quantity = (int) $request->query->get('quantity', 1);
        
        $this->cartService->add($product, $quantity, $this->getUser());
        
        $this->addFlash('success', 'Le produit a été ajouté au panier');
        
        if ($request->query->get('buy_now')) {
            return $this->redirectToRoute('cart_show');
        }
        
        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }

    /**
     * @Route("/panier/modifier/{id}", name="cart_update")
     */
    public function update(CartItem $item, Request $request): Response
    {
        $quantity = (int) $request->request->get('quantity');
        
        if ($item->getCart() !== $this->cartService->getCart($this->getUser())) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
        
        $this->cartService->updateQuantity($item, $quantity, $this->getUser());
        
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/panier/supprimer/{id}", name="cart_remove")
     */
    public function remove(CartItem $item): Response
    {
        if ($item->getCart() !== $this->cartService->getCart($this->getUser())) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
        
        $this->cartService->remove($item, $this->getUser());
        
        $this->addFlash('success', 'Le produit a été retiré du panier');
        
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/panier/vider", name="cart_clear")
     */
    public function clear(): Response
    {
        $this->cartService->clear($this->getUser());
        
        $this->addFlash('success', 'Le panier a été vidé');
        
        return $this->redirectToRoute('cart_show');
    }
}
