<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FollowupService;
use App\Service\GeolocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class FollowupController extends AbstractController
{
    public function __construct(
        private readonly FollowupService $followupService,
        private readonly GeolocationService $geolocation,
    ) {}

    #[Route('/followup/optin', name: 'followup_optin', methods: ['POST'])]
    public function optin(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('htmx', $request->headers->get('X-CSRF-Token'))) {
            throw new AccessDeniedException('Invalid CSRF token');
        }

        $email = filter_var($request->request->get('email', ''), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return new Response('', 400);
        }

        $countryCode = (string) $request->request->get('country', 'de');
        $locale = $request->getLocale();

        $this->followupService->schedule($email, $countryCode, $locale);

        return $this->render('followup/_confirmed.html.twig');
    }

    #[Route('/followup/stop/{token}', name: 'followup_stop')]
    public function stop(string $token): Response
    {
        $this->followupService->cancelByToken($token);
        return $this->render('followup/stopped.html.twig');
    }
}
