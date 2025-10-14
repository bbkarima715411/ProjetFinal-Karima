<?php

namespace App\Controller;

use App\Entity\Favori;
use App\Repository\FavoriRepository;
use App\Repository\LotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/favoris')]
#[IsGranted('ROLE_USER')]
class FavoriController extends AbstractController
{
    #[Route('/ajouter/{id}', name: 'app_favori_add', methods: ['POST'])]
    public function add(int $id, Request $request, EntityManagerInterface $em, FavoriRepository $repo, LotRepository $lotRepo): Response
    {
        $lot = $lotRepo->find($id);
        if (!$lot) {
            $this->addFlash('danger', 'Lot introuvable.');
            return $this->redirectToRoute('app_lot_index');
        }

        if (!$this->isCsrfTokenValid('fav_add_'.$lot->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$repo->isFavorite($user, $lot)) {
            $fav = new Favori();
            $fav->setUser($user)->setLot($lot);
            $em->persist($fav);
            try { $em->flush(); } catch (\Throwable) { /* unique constraint fallback */ }
        }

        $this->addFlash('success', 'Ajouté aux favoris.');
        return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
    }

    #[Route('/retirer/{id}', name: 'app_favori_remove', methods: ['POST'])]
    public function remove(int $id, Request $request, EntityManagerInterface $em, FavoriRepository $repo, LotRepository $lotRepo): Response
    {
        $lot = $lotRepo->find($id);
        if (!$lot) {
            $this->addFlash('danger', 'Lot introuvable.');
            return $this->redirectToRoute('app_lot_index');
        }

        if (!$this->isCsrfTokenValid('fav_remove_'.$lot->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $fav = $repo->findOneBy(['user' => $user, 'lot' => $lot]);
        if ($fav) {
            $em->remove($fav);
            $em->flush();
            $this->addFlash('success', 'Retiré des favoris.');
        }

        return $this->redirectToRoute('app_lot_show', ['id' => $lot->getId()]);
    }
}
