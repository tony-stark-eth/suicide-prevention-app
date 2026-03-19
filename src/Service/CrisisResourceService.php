<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Country;
use App\Entity\CrisisResource;
use App\Repository\CountryRepository;
use App\Repository\CrisisResourceRepository;

final class CrisisResourceService
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CrisisResourceRepository $resourceRepository,
    ) {}

    public function getCountry(string $countryCode): ?Country
    {
        return $this->countryRepository->find($countryCode);
    }

    public function getPrimary(string $countryCode): ?CrisisResource
    {
        return $this->resourceRepository->findPrimaryForCountry($countryCode);
    }

    /** @return array<string, CrisisResource[]> */
    public function getForCountry(string $countryCode): array
    {
        $resources = $this->resourceRepository->findByCountryCode($countryCode);
        $grouped = [];
        foreach ($resources as $resource) {
            $grouped[$resource->getType()][] = $resource;
        }
        return $grouped;
    }

    /** @return CrisisResource[] */
    public function getResourceList(string $countryCode): array
    {
        return $this->resourceRepository->findByCountryCode($countryCode);
    }

    /** @return Country[] */
    public function getAllCountries(): array
    {
        return $this->countryRepository->findAllOrderedByName();
    }
}
