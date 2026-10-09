<?php

namespace App\Support\Schema;

class BreadcrumbSchema
{
    /**
     * @param  list<array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public function make(array $items, string $pageUrl): array
    {
        $listItems = [];
        $seenUrls = [];

        foreach ($items as $item) {
            $name = trim($item['name']);
            $url = $item['url'];

            if ($name === '' || in_array($url, $seenUrls, true)) {
                continue;
            }

            $seenUrls[] = $url;
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => count($listItems) + 1,
                'name' => $name,
                'item' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => rtrim($pageUrl, '/').'#breadcrumb',
            'itemListElement' => $listItems,
        ];
    }
}
