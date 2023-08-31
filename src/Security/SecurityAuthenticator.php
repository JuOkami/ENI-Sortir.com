<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\ParticipantRepository;

class SecurityAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private ParticipantRepository $participantRepository,
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    // Méthode appelée lors de la tentative d'authentification
    public function authenticate(Request $request): Passport
    {
        $mail = $request->request->get('pseudo', '');
        $request->getSession()->set(Security::LAST_USERNAME, $mail);

        if (str_contains($mail, '@')) {
            // L'utilisateur se connecte avec un email
            $participant = $this->participantRepository->findOneBy(['mail' => $mail]);
        } else {
            // L'utilisateur se connecte avec un nom d'utilisateur
            $participant = $this->participantRepository->findOneBy(['pseudo' => $mail]);
        }
        if ($participant == null) {
            return new Passport(
                new UserBadge($mail), // Utilise l'email comme identifiant de l'utilisateur
                new PasswordCredentials($request->request->get('password', '')),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                    new RememberMeBadge(),
                ]
            );
        }
        return new Passport(
            new UserBadge($participant->getMail()),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    // Méthode appelée lorsque l'authentification réussit
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirige l'utilisateur vers la liste des sorties après la connexion réussie
        return new RedirectResponse($this->urlGenerator->generate('app_sorties_list'));
    }

    // Méthode pour obtenir l'URL de la page de connexion en cas d'échec d'authentification
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
