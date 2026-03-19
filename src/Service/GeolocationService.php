<?php

declare(strict_types=1);

namespace App\Service;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

final class GeolocationService
{
    public function __construct(
        private readonly string $geoipDbPath,
    ) {}

    public function detect(?string $ip): string
    {
        if (!$ip || !file_exists($this->geoipDbPath)) {
            return 'de';
        }

        try {
            $reader = new Reader($this->geoipDbPath);
            $record = $reader->country($ip);
            return strtolower($record->country->isoCode ?? 'de') ?: 'de';
        } catch (AddressNotFoundException) {
            return 'de';
        } catch (\Exception) {
            return 'de';
        }
    }
}
