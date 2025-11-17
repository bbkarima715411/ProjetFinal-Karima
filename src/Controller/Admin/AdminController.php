<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\LotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        LotRepository $lotRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $recent_orders = $orderRepository->findBy([], ['createdAt' => 'DESC'], 5);
            
            $total_users = $userRepository->count([]);
            
            $new_users_this_month = $userRepository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.createdAt >= :startDate')
                ->setParameter('startDate', new \DateTime('first day of this month'))
                ->getQuery()
                ->getSingleScalarResult();
                
            $recent_users = $userRepository->findBy([], ['createdAt' => 'DESC'], 5);

            // --- Vue d'ensemble des enchères ---
            $lots = $lotRepository->findAllWithValidEvent();

            $nbEncheresOuvertes = 0;
            $nbEncheresAVenir = 0;
            $nbEncheresTerminees = 0;
            $nbLotsVendus = 0;
            $encheresImminentes = [];

            $tz = new \DateTimeZone('Europe/Paris');
            $now = new \DateTimeImmutable('now', $tz);

            foreach ($lots as $lot) {
                $evenement = $lot->getEvenementEnchere();

                if ($lot->isEnchereOuverte()) {
                    $nbEncheresOuvertes++;
                } elseif ($evenement && $evenement->getDebutAt() instanceof \DateTimeImmutable && $now < $evenement->getDebutAt()) {
                    $nbEncheresAVenir++;
                }

                if ($lot->isEnchereTerminee()) {
                    $nbEncheresTerminees++;
                    if ($lot->getStatutFinal() === 'remportee') {
                        $nbLotsVendus++;
                    }
                }

                if ($evenement && $evenement->fermeDansMoinsDuneHeure($tz)) {
                    $encheresImminentes[] = $lot;
                }
            }

            // On limite la liste des enchères imminentes à 5
            $encheresImminentes = array_slice($encheresImminentes, 0, 5);

            return $this->render('admin/dashboard.html.twig', [
                'recent_orders' => $recent_orders,
                'total_users' => $total_users,
                'new_users_this_month' => $new_users_this_month,
                'recent_users' => $recent_users,
                'nb_encheres_ouvertes' => $nbEncheresOuvertes,
                'nb_encheres_a_venir' => $nbEncheresAVenir,
                'nb_encheres_terminees' => $nbEncheresTerminees,
                'nb_lots_vendus' => $nbLotsVendus,
                'encheres_imminentes' => $encheresImminentes,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors du chargement du tableau de bord : ' . $e->getMessage());
            
            // Retourner une réponse avec des tableaux vides en cas d'erreur
            return $this->render('admin/dashboard.html.twig', [
                'recent_orders' => [],
                'total_users' => 0,
                'new_users_this_month' => 0,
                'recent_users' => [],
                'nb_encheres_ouvertes' => 0,
                'nb_encheres_a_venir' => 0,
                'nb_encheres_terminees' => 0,
                'nb_lots_vendus' => 0,
                'encheres_imminentes' => [],
            ]);
        }
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
    public function newProduct(
        Request $request, 
        EntityManagerInterface $em, 
        CategoryRepository $catRepo, 
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();
        $categories = $catRepo->findAll();

        if ($request->isMethod('POST')) {
            $name = trim((string)$request->request->get('name'));
            $price = (float)$request->request->get('price');

            $product->setName($name);
            $product->setSlug(strtolower($slugger->slug($name)));
            $product->setShortDescription($request->request->get('short_description'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($price);
            $product->setStock($request->request->getInt('stock', 0));
            $product->setIsActive($request->request->getBoolean('is_active', true));
            $product->setReference($request->request->get('reference') ?: uniqid('PROD_'));

            if ($id = $request->request->get('category')) {
                if ($cat = $catRepo->find($id)) {
                    $product->setCategory($cat);
                }
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/new.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(
        Product $product, 
        Request $request, 
        EntityManagerInterface $em, 
        SluggerInterface $slugger, 
        CategoryRepository $catRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $name = trim((string)$request->request->get('name'));

            // si le nom change, on régénère le slug
            if ($name && $name !== $product->getName()) {
                $product->setSlug(strtolower($slugger->slug($name)));
            }
            
            $product->setName($name);
            $product->setShortDescription($request->request->get('short_description'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice((float)$request->request->get('price'));
            $product->setStock($request->request->getInt('stock', 0));
            $product->setIsActive($request->request->getBoolean('is_active', true));
            $product->setReference($request->request->get('reference') ?: $product->getReference());

            if ($id = $request->request->get('category')) {
                if ($cat = $catRepo->find($id)) {
                    $product->setCategory($cat);
                }
            } else {
                $product->setCategory(null);
            }

            $em->flush();
            $this->addFlash('success', 'Produit mis à jour.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
            'categories' => $catRepo->findAll(),
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
