<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['code' => 'de', 'nameEn' => 'Germany',                 'nameLocal' => 'Deutschland',              'primaryLanguage' => 'de', 'flag' => '🇩🇪', 'suicideIllegal' => false, 'policeDispatchRisk' => false],
            ['code' => 'kr', 'nameEn' => 'South Korea',              'nameLocal' => '대한민국',                  'primaryLanguage' => 'ko', 'flag' => '🇰🇷', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'ru', 'nameEn' => 'Russia',                   'nameLocal' => 'Россия',                   'primaryLanguage' => 'ru', 'flag' => '🇷🇺', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'jp', 'nameEn' => 'Japan',                    'nameLocal' => '日本',                      'primaryLanguage' => 'ja', 'flag' => '🇯🇵', 'suicideIllegal' => false, 'policeDispatchRisk' => false],
            ['code' => 'lt', 'nameEn' => 'Lithuania',                'nameLocal' => 'Lietuva',                  'primaryLanguage' => 'lt', 'flag' => '🇱🇹', 'suicideIllegal' => false, 'policeDispatchRisk' => false],
            ['code' => 'ua', 'nameEn' => 'Ukraine',                  'nameLocal' => 'Україна',                  'primaryLanguage' => 'uk', 'flag' => '🇺🇦', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'lv', 'nameEn' => 'Latvia',                   'nameLocal' => 'Latvija',                  'primaryLanguage' => 'lv', 'flag' => '🇱🇻', 'suicideIllegal' => false, 'policeDispatchRisk' => false],
            ['code' => 'za', 'nameEn' => 'South Africa',             'nameLocal' => 'South Africa',             'primaryLanguage' => 'en', 'flag' => '🇿🇦', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'by', 'nameEn' => 'Belarus',                  'nameLocal' => 'Беларусь',                 'primaryLanguage' => 'ru', 'flag' => '🇧🇾', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'hu', 'nameEn' => 'Hungary',                  'nameLocal' => 'Magyarország',             'primaryLanguage' => 'hu', 'flag' => '🇭🇺', 'suicideIllegal' => false, 'policeDispatchRisk' => false],
            ['code' => 'kz', 'nameEn' => 'Kazakhstan',               'nameLocal' => 'Қазақстан',                'primaryLanguage' => 'ru', 'flag' => '🇰🇿', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'ug', 'nameEn' => 'Uganda',                   'nameLocal' => 'Uganda',                   'primaryLanguage' => 'en', 'flag' => '🇺🇬', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'gy', 'nameEn' => 'Guyana',                   'nameLocal' => 'Guyana',                   'primaryLanguage' => 'en', 'flag' => '🇬🇾', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'sr', 'nameEn' => 'Suriname',                 'nameLocal' => 'Suriname',                 'primaryLanguage' => 'nl', 'flag' => '🇸🇷', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'mz', 'nameEn' => 'Mozambique',               'nameLocal' => 'Moçambique',               'primaryLanguage' => 'pt', 'flag' => '🇲🇿', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'zw', 'nameEn' => 'Zimbabwe',                 'nameLocal' => 'Zimbabwe',                 'primaryLanguage' => 'en', 'flag' => '🇿🇼', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'bw', 'nameEn' => 'Botswana',                 'nameLocal' => 'Botswana',                 'primaryLanguage' => 'en', 'flag' => '🇧🇼', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'ls', 'nameEn' => 'Lesotho',                  'nameLocal' => 'Lesotho',                  'primaryLanguage' => 'en', 'flag' => '🇱🇸', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'sz', 'nameEn' => 'Eswatini',                 'nameLocal' => 'Eswatini',                 'primaryLanguage' => 'en', 'flag' => '🇸🇿', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
            ['code' => 'cf', 'nameEn' => 'Central African Republic', 'nameLocal' => 'République centrafricaine','primaryLanguage' => 'fr', 'flag' => '🇨🇫', 'suicideIllegal' => false, 'policeDispatchRisk' => true],
        ];

        foreach ($countries as $data) {
            $country = new Country(
                code: $data['code'],
                nameEn: $data['nameEn'],
                nameLocal: $data['nameLocal'],
                primaryLanguage: $data['primaryLanguage'],
                flagEmoji: $data['flag'],
                suicideIllegal: $data['suicideIllegal'],
                policeDispatchRisk: $data['policeDispatchRisk'],
            );
            $manager->persist($country);
            $this->addReference('country_' . $data['code'], $country);
        }

        $manager->flush();
    }
}
