<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $urlGenerator;
    private $security;
    private $requestStack;

    public function __construct(
        UrlGeneratorInterface $urlGenerator, 
        Security $security,
        RequestStack $requestStack
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $session = $this->requestStack->getSession();
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
        if (!$user) {
            $session->getFlashBag()->add('error', 'Veuillez vous connecter pour accéder à cette page.');
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }
        
        // Si l'utilisateur n'a pas les droits admin, redirige vers la page d'accueil
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $session->getFlashBag()->add('warning', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
        
        return new RedirectResponse($this->urlGenerator->generate('app_accueil'));
    }
}
