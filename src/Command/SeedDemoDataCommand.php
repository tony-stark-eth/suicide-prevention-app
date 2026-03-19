<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Country;
use App\Entity\CrisisResource;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Seeds countries and crisis resources — no DoctrineFixturesBundle required.
 * Safe to run multiple times (skips existing records).
 */
#[AsCommand(name: 'app:seed', description: 'Seed countries and crisis resources')]
final class SeedDemoDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CountryRepository $countries,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // ── Countries ────────────────────────────────────────────────────────

        $countryData = [
            ['de', 'Germany',                 'Deutschland',               'de', '🇩🇪', false, false],
            ['kr', 'South Korea',              '대한민국',                   'ko', '🇰🇷', false, true],
            ['ru', 'Russia',                   'Россия',                    'ru', '🇷🇺', false, true],
            ['jp', 'Japan',                    '日本',                       'ja', '🇯🇵', false, false],
            ['lt', 'Lithuania',                'Lietuva',                   'lt', '🇱🇹', false, false],
            ['ua', 'Ukraine',                  'Україна',                   'uk', '🇺🇦', false, true],
            ['lv', 'Latvia',                   'Latvija',                   'lv', '🇱🇻', false, false],
            ['za', 'South Africa',             'South Africa',              'en', '🇿🇦', false, true],
            ['by', 'Belarus',                  'Беларусь',                  'ru', '🇧🇾', false, true],
            ['hu', 'Hungary',                  'Magyarország',              'hu', '🇭🇺', false, false],
            ['kz', 'Kazakhstan',               'Қазақстан',                 'ru', '🇰🇿', false, true],
            ['ug', 'Uganda',                   'Uganda',                    'en', '🇺🇬', false, true],
            ['gy', 'Guyana',                   'Guyana',                    'en', '🇬🇾', false, true],
            ['sr', 'Suriname',                 'Suriname',                  'nl', '🇸🇷', false, true],
            ['mz', 'Mozambique',               'Moçambique',                'pt', '🇲🇿', false, true],
            ['zw', 'Zimbabwe',                 'Zimbabwe',                  'en', '🇿🇼', false, true],
            ['bw', 'Botswana',                 'Botswana',                  'en', '🇧🇼', false, true],
            ['ls', 'Lesotho',                  'Lesotho',                   'en', '🇱🇸', false, true],
            ['sz', 'Eswatini',                 'Eswatini',                  'en', '🇸🇿', false, true],
            ['cf', 'Central African Republic', 'République centrafricaine', 'fr', '🇨🇫', false, true],
        ];

        $countryMap = [];
        $skippedCountries = 0;

        foreach ($countryData as [$code, $nameEn, $nameLocal, $lang, $flag, $illegal, $police]) {
            $existing = $this->countries->find($code);
            if ($existing !== null) {
                $countryMap[$code] = $existing;
                ++$skippedCountries;
                continue;
            }
            $country = new Country(
                code: $code,
                nameEn: $nameEn,
                nameLocal: $nameLocal,
                primaryLanguage: $lang,
                flagEmoji: $flag,
                suicideIllegal: $illegal,
                policeDispatchRisk: $police,
            );
            $this->em->persist($country);
            $countryMap[$code] = $country;
        }

        $this->em->flush();
        $io->success(sprintf('Countries: %d seeded, %d already existed.', count($countryMap) - $skippedCountries, $skippedCountries));

        // ── Crisis resources ─────────────────────────────────────────────────

        $resources = [
            // ── GERMANY
            ['de', 'hotline_phone', 'TelefonSeelsorge',                       '0800 111 0 111', 'https://www.telefonseelsorge.de', null, null,    true,  true,  true,  false, ['de'],     'Kostenlose telefonische Seelsorge, rund um die Uhr', 1],
            ['de', 'hotline_phone', 'TelefonSeelsorge (2. Leitung)',           '0800 111 0 222', null,                              null, null,    true,  true,  false, false, ['de'],     null,                                               2],
            ['de', 'hotline_chat',  'Online-Beratung TelefonSeelsorge',        null,             'https://online.telefonseelsorge.de', null, null, false, true,  false, false, ['de'],     'Chat und E-Mail-Beratung',                         3],
            ['de', 'hotline_phone', 'Nummer gegen Kummer (Erwachsene)',        '0800 111 0 550', null,                              null, null,    false, true,  false, false, ['de'],     null,                                               4],
            ['de', 'youth',         'Nummer gegen Kummer (Kinder und Jugendliche)', '116 111',   null,                              null, null,    false, true,  false, false, ['de'],     'Für Kinder und Jugendliche bis 18 Jahre',          5],
            ['de', 'therapist_finder', 'Therapeutensuche (KBV)',               null,             'https://www.kbv.de/html/arztsuche.php', null, null, false, true, false, false, ['de'],  'Kassenzulassung — über gesetzliche KV',            6],
            ['de', 'online_therapy', 'Deutsche Depressionshilfe',              null,             'https://www.deutsche-depressionshilfe.de', null, null, false, true, false, false, ['de'], 'Informationen, Selbsttests, Beratung',            7],
            // ── SOUTH KOREA
            ['kr', 'hotline_phone', '자살예방상담전화',                          '1393',           null,                              null, null,    true,  true,  true,  false, ['ko'],     '24시간 자살예방 상담전화',                           1],
            ['kr', 'hotline_phone', '정신건강 위기상담전화',                      '1577-0199',      null,                              null, null,    true,  true,  false, false, ['ko'],     null,                                               2],
            // ── RUSSIA
            ['ru', 'hotline_phone', 'Телефон доверия',                         '8-800-2000-122', null,                              null, null,    true,  true,  true,  false, ['ru'],     'Бесплатная психологическая помощь',                1],
            ['ru', 'hotline_phone', 'Экстренная психологическая помощь МЧС',   '8-800-775-17-17', null,                             null, null,    true,  true,  false, false, ['ru'],     null,                                               2],
            // ── JAPAN
            ['jp', 'hotline_phone', 'いのちの電話',                              '0120-783-556',   null,                              null, null,    true,  true,  true,  false, ['ja'],     '24時間対応の自殺防止ホットライン',                    1],
            ['jp', 'hotline_phone', 'よりそいホットライン',                       '0120-279-338',   null,                              null, null,    true,  true,  false, false, ['ja','en'], null,                                              2],
            // ── LITHUANIA
            ['lt', 'hotline_phone', 'Jaunimo linija',                          '8 800 28 888',   null,                              null, null,    true,  true,  true,  false, ['lt'],     'Nemokama pagalbos linija jaunimui',                1],
            ['lt', 'hotline_phone', 'Vilties linija',                          '116 123',         null,                              null, null,    true,  true,  false, false, ['lt'],     null,                                               2],
            // ── UKRAINE
            ['ua', 'hotline_phone', 'Телефон довіри',                          '7333',           null,                              null, null,    true,  true,  true,  false, ['uk'],     'Психологічна допомога цілодобово',                 1],
            ['ua', 'hotline_phone', 'Lifeline Ukraine',                        '7333',           'https://lifelineukraine.com',     null, null,    true,  true,  false, false, ['uk','en'], null,                                              2],
            // ── LATVIA
            ['lv', 'hotline_phone', 'Skalbes krīzes tālrunis',                 '116 123',         null,                              null, null,    true,  true,  true,  false, ['lv'],     null,                                               1],
            // ── SOUTH AFRICA
            ['za', 'hotline_phone', 'SADAG Suicide Crisis Line',               '0800 567 567',   null,                              null, null,    true,  true,  true,  false, ['en'],     '24/7 crisis support',                              1],
            ['za', 'hotline_text',  'SADAG SMS Line',                          null,             null,                              null, '31393', false, false, false, false, ['en'],     null,                                               2],
            // ── BELARUS
            ['by', 'hotline_phone', 'Республиканский телефон доверия',         '8-801-100-16-11', null,                             null, null,    false, true,  true,  false, ['ru'],     'Пн–Пт 9:00–21:00',                                1],
            // ── HUNGARY
            ['hu', 'hotline_phone', 'Lelkisegély Telefonszolgálat',            '116 123',         null,                              null, null,    true,  true,  true,  false, ['hu'],     null,                                               1],
            // ── KAZAKHSTAN
            ['kz', 'hotline_phone', 'Телефон доверия',                         '150',            null,                              null, null,    true,  true,  true,  false, ['ru','kk'], null,                                              1],
            // ── UGANDA
            ['ug', 'hotline_phone', 'Befrienders Uganda',                      '0800 212 121',   null,                              null, null,    false, true,  true,  false, ['en'],     null,                                               1],
            // ── GUYANA
            ['gy', 'hotline_phone', 'Guyana Crisis Helpline',                  '223-0001',        null,                              null, null,    false, true,  true,  false, ['en'],     null,                                               1],
            // ── SURINAME
            ['sr', 'hotline_phone', 'SOS Telefoon Suriname',                   '420-617',         null,                              null, null,    false, false, true,  false, ['nl'],     null,                                               1],
            // ── MOZAMBIQUE
            ['mz', 'hotline_chat',  'Find A Helpline',                         null,             'https://findahelpline.com/mz',    null, null,    false, true,  true,  false, ['pt'],     'Directory of local services',                      1],
            // ── ZIMBABWE
            ['zw', 'hotline_phone', 'CCSL Zimbabwe',                           '0800 9000',       null,                              null, null,    false, true,  true,  false, ['en'],     null,                                               1],
            // ── BOTSWANA
            ['bw', 'hotline_phone', 'Lifeline Botswana',                       '+267 3911270',    null,                              null, null,    false, true,  true,  false, ['en'],     null,                                               1],
            // ── LESOTHO
            ['ls', 'hotline_chat',  'Befrienders Worldwide',                   null,             'https://www.befrienders.org',     null, null,    false, true,  true,  false, ['en'],     'International fallback — no local line available', 1],
            // ── ESWATINI
            ['sz', 'hotline_chat',  'Find A Helpline',                         null,             'https://findahelpline.com/sz',    null, null,    false, true,  true,  false, ['en'],     null,                                               1],
            // ── CENTRAL AFRICAN REPUBLIC
            ['cf', 'hotline_chat',  'Find A Helpline',                         null,             'https://findahelpline.com',       null, null,    false, true,  true,  false, ['fr'],     'International directory',                          1],
        ];

        $seeded = 0;
        foreach ($resources as [$cc, $type, $name, $phone, $url, $textNum, $sms, $is24h, $isFree, $isPrimary, $noPolice, $langs, $desc, $sort]) {
            $country = $countryMap[$cc] ?? null;
            if ($country === null) {
                continue;
            }
            $resource = new CrisisResource(
                country: $country,
                type: $type,
                name: $name,
                is24h: $is24h,
                isFree: $isFree,
                description: $desc,
                phone: $phone,
                url: $url,
                textNumber: $sms,
                noPoliceByDefault: $noPolice,
                isPrimary: $isPrimary,
                languages: $langs,
                sortOrder: $sort,
            );
            $this->em->persist($resource);
            ++$seeded;
        }

        $this->em->flush();
        $io->success(sprintf('Crisis resources: %d seeded.', $seeded));

        return Command::SUCCESS;
    }
}
