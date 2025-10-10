<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET','POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        $honeypot = $form->has('website') ? $form->get('website')->getData() : null;
        $lastTs = $request->getSession()->get('contact_last_submit_ts');
        $tooSoon = $lastTs && (time() - (int)$lastTs) < 10;

        if ($form->isSubmitted() && $form->isValid() && !$honeypot && !$tooSoon) {
            // Pour l'instant: pas d'envoi SMTP. On affiche juste un succès et on rafraîchit la page (PRG)
            $request->getSession()->set('contact_last_submit_ts', time());
            $this->addFlash('success', "Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.");
            return $this->redirectToRoute('app_contact');
        } elseif ($form->isSubmitted()) {
            if ($honeypot || $tooSoon) {
                $this->addFlash('warning', 'Veuillez patienter avant un nouvel envoi.');
            } else {
                $this->addFlash('danger', 'Veuillez corriger les champs du formulaire.');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
