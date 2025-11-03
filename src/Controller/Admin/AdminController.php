<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $recentOrders = method_exists($orderRepository, 'findRecentOrders') ? $orderRepository->findRecentOrders(5) : [];
        $lowStockProducts = method_exists($productRepository, 'findLowStock') ? $productRepository->findLowStock(5) : [];
        $totalUsers = $userRepository->count([]);
        $newUsersThisMonth = method_exists($userRepository, 'countNewUsersThisMonth') ? $userRepository->countNewUsersThisMonth() : 0;
        $recentUsers = method_exists($userRepository, 'findRecentUsers') ? $userRepository->findRecentUsers(5) : [];

        return $this->render('admin/dashboard.html.twig', compact(
            'recentOrders','lowStockProducts','totalUsers','newUsersThisMonth','recentUsers'
        ));
    }

    #[Route('/orders', name: 'admin_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/order/{id}', name: 'admin_order_show')]
    public function orderShow(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/orders/show.html.twig', ['order' => $order]);
    }

    #[Route('/products', name: 'admin_products')]
    public function products(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/products/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/new', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();
        $categories = $categoryRepository->findAll();

        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setShortDescription($request->request->get('short_description'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice((float)$request->request->get('price'));
            $product->setStock((int)$request->request->get('stock', 0));
            $product->setReference(uniqid('PROD_'));
            $product->setIsActive($request->request->has('is_active'));

            if ($id = $request->request->get('category')) {
                if ($cat = $categoryRepository->find($id)) {
                    $product->setCategory($cat);
                }
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/new.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setShortDescription($request->request->get('short_description'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice((float)$request->request->get('price'));
            $product->setStock((int)$request->request->get('stock', 0));
            $product->setIsActive($request->request->has('is_active'));

            if ($id = $request->request->get('category')) {
                $cat = $categoryRepository->find($id);
                $product->setCategory($cat);
            } else {
                $product->setCategory(null);
            }

            $em->flush();

            $this->addFlash('success', 'Produit mis à jour avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('admin_products');
    }
}
