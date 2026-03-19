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
    public function __construct(
        private readonly GeolocationService $geolocation,
        private readonly CrisisResourceService $crisisResources,
    ) {}

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
