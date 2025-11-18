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

// Je suis le contrôleur qui gère tout ce qui concerne le panier (affichage, ajout, modification, suppression)
class CartController extends AbstractController
{
    private $cartService;
    private $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        // J'injecte mon service de panier et l'entity manager pour pouvoir manipuler les données
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/panier", name="cart_show")
     */
    public function show(): Response
    {
        // Je récupère le panier associé à l'utilisateur connecté
        $cart = $this->cartService->getCart($this->getUser());
        
        // J'affiche la page panier avec le contenu du panier et le total calculé par le service
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
        // Je récupère la quantité à ajouter (par défaut 1 si rien n'est passé)
        $quantity = (int) $request->query->get('quantity', 1);
        
        // J'ajoute le produit au panier de l'utilisateur connecté
        $this->cartService->add($product, $quantity, $this->getUser());
        
        // J'affiche un message de confirmation
        $this->addFlash('success', 'Le produit a été ajouté au panier');
        
        // Si l'utilisateur a cliqué sur "Acheter maintenant", je le redirige directement vers le panier
        if ($request->query->get('buy_now')) {
            return $this->redirectToRoute('cart_show');
        }
        
        // Sinon, je le renvoie sur la page du produit
        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }

    /**
     * @Route("/panier/modifier/{id}", name="cart_update")
     */
    public function update(CartItem $item, Request $request): Response
    {
        // Je récupère la nouvelle quantité envoyée depuis le formulaire
        $quantity = (int) $request->request->get('quantity');
        
        // Je vérifie que l'item appartient bien au panier de l'utilisateur connecté
        if ($item->getCart() !== $this->cartService->getCart($this->getUser())) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
        
        // Je mets à jour la quantité de cet article dans le panier
        $this->cartService->updateQuantity($item, $quantity, $this->getUser());
        
        // Je renvoie l'utilisateur sur la page du panier
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/panier/supprimer/{id}", name="cart_remove")
     */
    public function remove(CartItem $item): Response
    {
        // Je vérifie que l'item appartient bien au panier de l'utilisateur connecté
        if ($item->getCart() !== $this->cartService->getCart($this->getUser())) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
        
        // Je supprime l'article du panier
        $this->cartService->remove($item, $this->getUser());
        
        // J'informe l'utilisateur que le produit a bien été retiré
        $this->addFlash('success', 'Le produit a été retiré du panier');
        
        // Je renvoie vers la page panier
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/panier/vider", name="cart_clear")
     */
    public function clear(): Response
    {
        // Je vide complètement le panier de l'utilisateur connecté
        $this->cartService->clear($this->getUser());
        
        // J'affiche un message pour confirmer que le panier est vide
        $this->addFlash('success', 'Le panier a été vidé');
        
        // Je renvoie l'utilisateur vers la page panier (vide)
        return $this->redirectToRoute('cart_show');
    }
}

