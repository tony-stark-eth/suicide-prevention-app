<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CrisisResource;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CrisisResourceFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [CountryFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $resources = [
            // ── GERMANY
            ['country'=>'de','type'=>'hotline_phone','name'=>'TelefonSeelsorge',
             'phone'=>'0800 111 0 111','url'=>'https://www.telefonseelsorge.de',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['de'],'description'=>'Kostenlose telefonische Seelsorge, rund um die Uhr','sort'=>1],

            ['country'=>'de','type'=>'hotline_phone','name'=>'TelefonSeelsorge (2. Leitung)',
             'phone'=>'0800 111 0 222',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'sort'=>2],

            ['country'=>'de','type'=>'hotline_chat','name'=>'Online-Beratung TelefonSeelsorge',
             'url'=>'https://online.telefonseelsorge.de',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'description'=>'Chat und E-Mail-Beratung','sort'=>3],

            ['country'=>'de','type'=>'hotline_phone','name'=>'Nummer gegen Kummer (Erwachsene)',
             'phone'=>'0800 111 0 550',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'sort'=>4],

            ['country'=>'de','type'=>'youth','name'=>'Nummer gegen Kummer (Kinder und Jugendliche)',
             'phone'=>'116 111',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'description'=>'Für Kinder und Jugendliche bis 18 Jahre','sort'=>5],

            ['country'=>'de','type'=>'therapist_finder','name'=>'Therapeutensuche (KBV)',
             'url'=>'https://www.kbv.de/html/arztsuche.php',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'description'=>'Kassenzulassung — über gesetzliche KV','sort'=>6],

            ['country'=>'de','type'=>'online_therapy','name'=>'Deutsche Depressionshilfe',
             'url'=>'https://www.deutsche-depressionshilfe.de',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['de'],'description'=>'Informationen, Selbsttests, Beratung','sort'=>7],

            // ── SOUTH KOREA
            ['country'=>'kr','type'=>'hotline_phone','name'=>'자살예방상담전화',
             'phone'=>'1393',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['ko'],'description'=>'24시간 자살예방 상담전화','sort'=>1],

            ['country'=>'kr','type'=>'hotline_phone','name'=>'정신건강 위기상담전화',
             'phone'=>'1577-0199',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['ko'],'sort'=>2],

            // ── RUSSIA
            ['country'=>'ru','type'=>'hotline_phone','name'=>'Телефон доверия',
             'phone'=>'8-800-2000-122',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['ru'],'description'=>'Бесплатная психологическая помощь','sort'=>1],

            ['country'=>'ru','type'=>'hotline_phone','name'=>'Экстренная психологическая помощь МЧС',
             'phone'=>'8-800-775-17-17',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['ru'],'sort'=>2],

            // ── JAPAN
            ['country'=>'jp','type'=>'hotline_phone','name'=>'いのちの電話',
             'phone'=>'0120-783-556',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['ja'],'description'=>'24時間対応の自殺防止ホットライン','sort'=>1],

            ['country'=>'jp','type'=>'hotline_phone','name'=>'よりそいホットライン',
             'phone'=>'0120-279-338',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['ja','en'],'sort'=>2],

            // ── LITHUANIA
            ['country'=>'lt','type'=>'hotline_phone','name'=>'Jaunimo linija',
             'phone'=>'8 800 28 888',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['lt'],'description'=>'Nemokama pagalbos linija jaunimui','sort'=>1],

            ['country'=>'lt','type'=>'hotline_phone','name'=>'Vilties linija',
             'phone'=>'116 123',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['lt'],'sort'=>2],

            // ── UKRAINE
            ['country'=>'ua','type'=>'hotline_phone','name'=>'Телефон довіри',
             'phone'=>'7333',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['uk'],'description'=>'Психологічна допомога цілодобово','sort'=>1],

            ['country'=>'ua','type'=>'hotline_phone','name'=>'Lifeline Ukraine',
             'phone'=>'7333','url'=>'https://lifelineukraine.com',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['uk','en'],'sort'=>2],

            // ── LATVIA
            ['country'=>'lv','type'=>'hotline_phone','name'=>'Skalbes krīzes tālrunis',
             'phone'=>'116 123',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['lv'],'sort'=>1],

            // ── SOUTH AFRICA
            ['country'=>'za','type'=>'hotline_phone','name'=>'SADAG Suicide Crisis Line',
             'phone'=>'0800 567 567',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'description'=>'24/7 crisis support','sort'=>1],

            ['country'=>'za','type'=>'hotline_text','name'=>'SADAG SMS Line',
             'textNumber'=>'31393',
             'is24h'=>false,'isFree'=>false,'isPrimary'=>false,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>2],

            // ── BELARUS
            ['country'=>'by','type'=>'hotline_phone','name'=>'Республиканский телефон доверия',
             'phone'=>'8-801-100-16-11',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['ru'],'description'=>'Пн–Пт 9:00–21:00','sort'=>1],

            // ── HUNGARY
            ['country'=>'hu','type'=>'hotline_phone','name'=>'Lelkisegély Telefonszolgálat',
             'phone'=>'116 123',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['hu'],'sort'=>1],

            // ── KAZAKHSTAN
            ['country'=>'kz','type'=>'hotline_phone','name'=>'Телефон доверия',
             'phone'=>'150',
             'is24h'=>true,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['ru','kk'],'sort'=>1],

            // ── UGANDA
            ['country'=>'ug','type'=>'hotline_phone','name'=>'Befrienders Uganda',
             'phone'=>'0800 212 121',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>1],

            // ── GUYANA
            ['country'=>'gy','type'=>'hotline_phone','name'=>'Guyana Crisis Helpline',
             'phone'=>'223-0001',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>1],

            // ── SURINAME
            ['country'=>'sr','type'=>'hotline_phone','name'=>'SOS Telefoon Suriname',
             'phone'=>'420-617',
             'is24h'=>false,'isFree'=>false,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['nl'],'sort'=>1],

            // ── MOZAMBIQUE
            ['country'=>'mz','type'=>'hotline_chat','name'=>'Find A Helpline',
             'url'=>'https://findahelpline.com/mz',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['pt'],'description'=>'Directory of local services','sort'=>1],

            // ── ZIMBABWE
            ['country'=>'zw','type'=>'hotline_phone','name'=>'CCSL Zimbabwe',
             'phone'=>'0800 9000',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>1],

            // ── BOTSWANA
            ['country'=>'bw','type'=>'hotline_phone','name'=>'Lifeline Botswana',
             'phone'=>'+267 3911270',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>1],

            // ── LESOTHO
            ['country'=>'ls','type'=>'hotline_chat','name'=>'Befrienders Worldwide',
             'url'=>'https://www.befrienders.org',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'description'=>'International fallback — no local line available','sort'=>1],

            // ── ESWATINI
            ['country'=>'sz','type'=>'hotline_chat','name'=>'Find A Helpline',
             'url'=>'https://findahelpline.com/sz',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['en'],'sort'=>1],

            // ── CENTRAL AFRICAN REPUBLIC
            ['country'=>'cf','type'=>'hotline_chat','name'=>'Find A Helpline',
             'url'=>'https://findahelpline.com',
             'is24h'=>false,'isFree'=>true,'isPrimary'=>true,'noPoliceByDefault'=>false,
             'languages'=>['fr'],'description'=>'International directory','sort'=>1],
        ];

        foreach ($resources as $data) {
            /** @var \App\Entity\Country $country */
            $country = $this->getReference('country_' . $data['country'], \App\Entity\Country::class);
            $resource = new CrisisResource(
                country: $country,
                type: $data['type'],
                name: $data['name'],
                is24h: $data['is24h'],
                isFree: $data['isFree'],
                description: $data['description'] ?? null,
                phone: $data['phone'] ?? null,
                url: $data['url'] ?? null,
                textNumber: $data['textNumber'] ?? null,
                noPoliceByDefault: $data['noPoliceByDefault'],
                isPrimary: $data['isPrimary'],
                languages: $data['languages'] ?? null,
                sortOrder: $data['sort'],
            );
            $manager->persist($resource);
        }

        $manager->flush();
    }
}
