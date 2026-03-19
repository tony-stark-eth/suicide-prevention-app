<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CrisisResourceService;
use App\Service\GeolocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResourceController extends AbstractController
{
    public function __construct(
        private readonly GeolocationService $geolocation,
        private readonly CrisisResourceService $crisisResources,
    ) {}

    #[Route('/resources', name: 'resources')]
    public function index(Request $request): Response
    {
        $countryCode = $this->geolocation->detect($request->getClientIp());
        $allCountries = $this->crisisResources->getAllCountries();
        $resources = $this->crisisResources->getResourceList($countryCode);

        return $this->render('resources/index.html.twig', [
            'all_countries' => $allCountries,
            'initial_country' => $countryCode,
            'resources' => $resources,
        ]);
    }

    #[Route('/resources/{countryCode}', name: 'resources_country')]
    public function country(string $countryCode): Response
    {
        $resources = $this->crisisResources->getResourceList($countryCode);

        return $this->render('resources/_country.html.twig', [
            'resources' => $resources,
        ]);
    }
}
