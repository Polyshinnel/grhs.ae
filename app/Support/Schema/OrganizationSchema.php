<?php

namespace App\Support\Schema;

use App\Models\ContactSettings;

class OrganizationSchema
{
    private const ORGANIZATION_ID = 'https://grhs.ae/#organization';

    /**
     * @return array<string, mixed>
     */
    public function make(?ContactSettings $settings): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WholesaleStore',
            '@id' => self::ORGANIZATION_ID,
            'name' => 'GRHS',
            'legalName' => 'GRHS Trading LLC',
            'url' => 'https://grhs.ae/',
            'logo' => 'https://grhs.ae/images/site/logo.png',
            'description' => 'HoReCa supplier of tableware, glassware, cutlery, barware and kitchenware for hotels and restaurants in the UAE.',
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'United Arab Emirates',
            ],
        ];

        $telephone = $this->normalizeTelephone($settings?->phone);

        if ($telephone !== null) {
            $schema['telephone'] = $telephone;
        }

        $email = trim((string) $settings?->email);

        if ($email !== '') {
            $schema['email'] = $email;
        }

        $address = $this->streetAddress($settings?->address);

        if ($address !== null) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => 'Dubai',
                'addressCountry' => 'AE',
            ];
        }

        $socialLinks = $this->socialProfileUrls($settings?->social_links);

        if ($socialLinks !== []) {
            $schema['sameAs'] = $socialLinks;
        }

        return $schema;
    }

    private function normalizeTelephone(?string $telephone): ?string
    {
        $telephone = trim((string) $telephone);

        if ($telephone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $telephone);

        if ($digits === null || $digits === '') {
            return null;
        }

        return str_starts_with($telephone, '+') ? '+'.$digits : $digits;
    }

    private function streetAddress(?string $address): ?string
    {
        $address = trim((string) $address);

        if ($address === '') {
            return null;
        }

        $streetAddress = preg_replace('/,\s*Dubai\s*,\s*UAE\s*$/i', '', $address);
        $streetAddress = trim((string) $streetAddress, " \t\n\r\0\x0B,");

        return $streetAddress !== '' ? $streetAddress : null;
    }

    /**
     * @param  array<mixed>|null  $socialLinks
     * @return list<string>
     */
    private function socialProfileUrls(?array $socialLinks): array
    {
        $platformDomains = [
            'instagram' => ['instagram.com'],
            'facebook' => ['facebook.com'],
            'linkedin' => ['linkedin.com'],
            'youtube' => ['youtube.com'],
            'tiktok' => ['tiktok.com'],
            'x' => ['x.com', 'twitter.com'],
            'twitter' => ['x.com', 'twitter.com'],
        ];
        $profileUrls = [];

        foreach ($socialLinks ?? [] as $socialLink) {
            if (! is_array($socialLink)) {
                continue;
            }

            $platform = strtolower(trim((string) ($socialLink['platform'] ?? '')));
            $url = trim((string) ($socialLink['url'] ?? ''));
            $parts = parse_url($url);

            $host = is_array($parts) ? strtolower((string) ($parts['host'] ?? '')) : '';
            $hasExpectedHost = collect($platformDomains[$platform] ?? [])
                ->contains(fn (string $domain): bool => $host === $domain || str_ends_with($host, '.'.$domain));

            if (! $hasExpectedHost
                || $url === ''
                || filter_var($url, FILTER_VALIDATE_URL) === false
                || ! is_array($parts)
                || ! in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
                || $host === '') {
                continue;
            }

            if (! in_array($url, $profileUrls, true)) {
                $profileUrls[] = $url;
            }
        }

        return $profileUrls;
    }
}
