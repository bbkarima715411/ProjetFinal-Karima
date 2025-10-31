<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(OrderRepository $orderRepository, ProductRepository $productRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $recentOrders = $orderRepository->findRecentOrders(5);
        $lowStockProducts = $productRepository->findLowStock(5);
        
        // Statistiques des utilisateurs
        $totalUsers = $userRepository->count([]);
        $newUsersThisMonth = $userRepository->countNewUsersThisMonth();
        $recentUsers = $userRepository->findRecentUsers(5);
        
        return $this->render('admin/dashboard.html.twig', [
            'recent_orders' => $recentOrders,
            'low_stock_products' => $lowStockProducts,
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'recent_users' => $recentUsers,
        ]);
    }

    #[Route('/orders', name: 'admin_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $orders = $orderRepository->findAll();
        
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'admin_order_show')]
    public function orderShow(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/update-status', name: 'admin_order_update_status', methods: ['POST'])]
    public function updateOrderStatus(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $newStatus = $request->request->get('status');
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (in_array($newStatus, $validStatuses)) {
            $order->setStatus($newStatus);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le statut de la commande a été mis à jour.');
        } else {
            $this->addFlash('error', 'Statut invalide.');
        }
        
        return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
    }

    #[Route('/products', name: 'admin_products')]
    public function products(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $products = $productRepository->findAll();
        
        return $this->render('admin/products/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $product = new Product();
        
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock($request->request->getInt('stock', 0));
            $product->setReference(uniqid('PROD_'));
            
            $entityManager->persist($product);
            $entityManager->flush();
            
            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_products');
        }
        
        return $this->render('admin/products/new.html.twig');
    }

    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock($request->request->getInt('stock', 0));
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Produit mis à jour avec succès.');
            return $this->redirectToRoute('admin_products');
        }
        
        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $entityManager->remove($product);
        $entityManager->flush();
        
        $this->addFlash('success', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('admin_products');
    }
}
