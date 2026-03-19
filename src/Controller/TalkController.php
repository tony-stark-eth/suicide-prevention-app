<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CrisisResourceService;
use App\Service\GeolocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TalkController extends AbstractController
{
    public function __construct(
        private readonly GeolocationService $geolocation,
        private readonly CrisisResourceService $crisisResources,
    ) {}

    #[Route('/talk', name: 'talk')]
    public function index(Request $request): Response
    {
        $countryCode = $this->geolocation->detect($request->getClientIp());
        $country = $this->crisisResources->getCountry($countryCode);
        $resources = $this->crisisResources->getResourceList($countryCode);

        return $this->render('talk/index.html.twig', [
            'country_code' => $countryCode,
            'country' => $country,
            'resources' => $resources,
        ]);
    }

    #[Route('/talk/transparency/{country}', name: 'talk_transparency')]
    public function transparency(string $country): Response
    {
        $countryEntity = $this->crisisResources->getCountry($country);

        return $this->render('talk/_transparency.html.twig', [
            'country' => $countryEntity,
            'country_code' => $country,
        ]);
    }
}
