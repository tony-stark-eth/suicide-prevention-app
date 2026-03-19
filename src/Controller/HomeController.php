<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CrisisResourceService;
use App\Service\GeolocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    private const SUPPORTED_LOCALES = ['de', 'en', 'ru', 'ko', 'ja', 'lt', 'uk', 'es'];

    public function __construct(
        private readonly GeolocationService $geolocation,
        private readonly CrisisResourceService $crisisResources,
    ) {}

    /** Registered in routes.yaml (no locale prefix). Redirects to the locale home page. */
    public function root(Request $request): Response
    {
        $countryCode = $this->geolocation->detect($request->getClientIp());
        $country = $this->crisisResources->getCountry($countryCode);

        $locale = $country?->getPrimaryLanguage() ?? 'de';
        if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'en';
        }

        return $this->redirectToRoute('home', ['_locale' => $locale]);
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $countryCode = $this->geolocation->detect($request->getClientIp());
        $country = $this->crisisResources->getCountry($countryCode);
        $primaryResource = $this->crisisResources->getPrimary($countryCode);

        return $this->render('home/index.html.twig', [
            'country_code' => $countryCode,
            'country' => $country,
            'primary_resource' => $primaryResource,
        ]);
    }
}
