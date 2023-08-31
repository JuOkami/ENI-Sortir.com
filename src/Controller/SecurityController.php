<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        AuthenticationUtils   $authenticationUtils,
        ParticipantRepository $participantRepository,
    ): Response
    {
        // Récupération de l'éventuelle erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérification si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            $user = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            if (!$user->isActif()) {
                // Redirection vers la déconnexion si l'utilisateur n'est pas actif

                return $this->redirectToRoute('app_logout');
            }
        } else {
            $user = null;
        }

        // Affichage du formulaire de connexion avec les éventuelles erreurs
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // Route pour la déconnexion (gérée par le firewall)
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
