# Проверка редиректов каталогов

Источник сопоставлений: `docs/legacy-catalogue-url-mapping.md`.

Конечные адреса проверены запросами **GET** с `Range: bytes=0-1023`; читалось не более 1024 байт ответа. Успех: HTTP 2xx, Content-Type с `pdf` и сигнатура `%PDF-`. HEAD не использовался.

## Сводка

- Сопоставлений в источнике: 113.
- Уникальных конечных URL проверено: 113.
- Готовых редиректов в конфиге: 113.
- Ошибок конечных URL: 0.
- Повторяющихся исходных путей: 0.
- Конфликтующих назначений: 0.

## Проверка сопоставлений

Повторяющиеся исходные пути: нет.
Конфликтующие назначения: нет.

## Исключённые редиректы

Нет: все конечные URL прошли проверку.

## Все конечные URL

| URL | HTTP | Content-Type | Range | Результат |
|---|---:|---|---|---|
| `https://grhs.ae/catalogues/1/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/KENAI.pdf` | 206 | application/pdf | bytes 0-1023/2581217 | PDF подтверждён |
| `https://grhs.ae/catalogues/2/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/ucello.pdf` | 206 | application/pdf | bytes 0-1023/4751714 | PDF подтверждён |
| `https://grhs.ae/catalogues/3/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/le-coq.pdf` | 206 | application/pdf | bytes 0-1023/25830584 | PDF подтверждён |
| `https://grhs.ae/catalogues/4/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/GIEN.pdf` | 206 | application/pdf | bytes 0-1023/21508642 | PDF подтверждён |
| `https://grhs.ae/catalogues/5/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/Bitossi-home.pdf` | 206 | application/pdf | bytes 0-1023/7356183 | PDF подтверждён |
| `https://grhs.ae/catalogues/6/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/miyama.pdf` | 206 | application/pdf | bytes 0-1023/13995050 | PDF подтверждён |
| `https://grhs.ae/catalogues/7/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/pura-sangre.pdf` | 206 | application/pdf | bytes 0-1023/13086394 | PDF подтверждён |
| `https://grhs.ae/catalogues/8/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/arita.pdf` | 206 | application/pdf | bytes 0-1023/891815 | PDF подтверждён |
| `https://grhs.ae/catalogues/9/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/arita-plus.pdf` | 206 | application/pdf | bytes 0-1023/1386174 | PDF подтверждён |
| `https://grhs.ae/catalogues/10/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/arita-plus2.pdf` | 206 | application/pdf | bytes 0-1023/2549515 | PDF подтверждён |
| `https://grhs.ae/catalogues/11/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/bespoke-uae.pdf` | 206 | application/pdf | bytes 0-1023/2608753 | PDF подтверждён |
| `https://grhs.ae/catalogues/12/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/ucello-handmade.pdf` | 206 | application/pdf | bytes 0-1023/3540789 | PDF подтверждён |
| `https://grhs.ae/catalogues/13/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/soul-studio.pdf` | 206 | application/pdf | bytes 0-1023/992633 | PDF подтверждён |
| `https://grhs.ae/catalogues/14/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/yoshinuma-glass.pdf` | 206 | application/pdf | bytes 0-1023/7057858 | PDF подтверждён |
| `https://grhs.ae/catalogues/15/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/toyo-sasaki.pdf` | 206 | application/pdf | bytes 0-1023/3608000 | PDF подтверждён |
| `https://grhs.ae/catalogues/16/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/sklarna.pdf` | 206 | application/pdf | bytes 0-1023/4298942 | PDF подтверждён |
| `https://grhs.ae/catalogues/17/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/solid-water.pdf` | 206 | application/pdf | bytes 0-1023/3868462 | PDF подтверждён |
| `https://grhs.ae/catalogues/18/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/aoyama-glass.pdf` | 206 | application/pdf | bytes 0-1023/5190195 | PDF подтверждён |
| `https://grhs.ae/catalogues/19/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/glass-made.pdf` | 206 | application/pdf | bytes 0-1023/9012476 | PDF подтверждён |
| `https://grhs.ae/catalogues/20/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/kurieto.pdf` | 206 | application/pdf | bytes 0-1023/10432839 | PDF подтверждён |
| `https://grhs.ae/catalogues/21/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/molleni.pdf` | 206 | application/pdf | bytes 0-1023/4450494 | PDF подтверждён |
| `https://grhs.ae/catalogues/22/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/tableware/hirota.pdf` | 206 | application/pdf | bytes 0-1023/2397112 | PDF подтверждён |
| `https://grhs.ae/catalogues/23/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/birdy.pdf` | 206 | application/pdf | bytes 0-1023/4747037 | PDF подтверждён |
| `https://grhs.ae/catalogues/24/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/coctail-kingdom.pdf` | 206 | application/pdf | bytes 0-1023/1872733 | PDF подтверждён |
| `https://grhs.ae/catalogues/25/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/aderia.pdf` | 206 | application/pdf | bytes 0-1023/22396344 | PDF подтверждён |
| `https://grhs.ae/catalogues/26/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/aoyama-glass.pdf` | 206 | application/pdf | bytes 0-1023/7366327 | PDF подтверждён |
| `https://grhs.ae/catalogues/27/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/kimura.pdf` | 206 | application/pdf | bytes 0-1023/5078595 | PDF подтверждён |
| `https://grhs.ae/catalogues/28/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/sip-guzzle.pdf` | 206 | application/pdf | bytes 0-1023/1049482 | PDF подтверждён |
| `https://grhs.ae/catalogues/29/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/gabriel-glass.pdf` | 206 | application/pdf | bytes 0-1023/512335 | PDF подтверждён |
| `https://grhs.ae/catalogues/30/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/barware/toyo-sasaki.pdf` | 206 | application/pdf | bytes 0-1023/10859401 | PDF подтверждён |
| `https://grhs.ae/catalogues/31/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/cutlery/abert.pdf` | 206 | application/pdf | bytes 0-1023/4676397 | PDF подтверждён |
| `https://grhs.ae/catalogues/32/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/cutlery/sabre.pdf` | 206 | application/pdf | bytes 0-1023/5314657 | PDF подтверждён |
| `https://grhs.ae/catalogues/33/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/cutlery/eme.pdf` | 206 | application/pdf | bytes 0-1023/15301722 | PDF подтверждён |
| `https://grhs.ae/catalogues/34/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/cutlery/le-coq.pdf` | 206 | application/pdf | bytes 0-1023/8434972 | PDF подтверждён |
| `https://grhs.ae/catalogues/35/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/steak-knives/abert.pdf` | 206 | application/pdf | bytes 0-1023/165271 | PDF подтверждён |
| `https://grhs.ae/catalogues/36/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/steak-knives/jean-dubost.pdf` | 206 | application/pdf | bytes 0-1023/1476814 | PDF подтверждён |
| `https://grhs.ae/catalogues/37/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/steak-knives/florentin-kitchen.pdf` | 206 | application/pdf | bytes 0-1023/1224191 | PDF подтверждён |
| `https://grhs.ae/catalogues/38/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/wood/trud.pdf` | 206 | application/pdf | bytes 0-1023/2340120 | PDF подтверждён |
| `https://grhs.ae/catalogues/39/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/wood/woodez.pdf` | 206 | application/pdf | bytes 0-1023/1965759 | PDF подтверждён |
| `https://grhs.ae/catalogues/40/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/kitchen-accesories/ilsa.pdf` | 206 | application/pdf | bytes 0-1023/21477944 | PDF подтверждён |
| `https://grhs.ae/catalogues/41/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/asian-concepts/youbi.pdf` | 206 | application/pdf | bytes 0-1023/87924749 | PDF подтверждён |
| `https://grhs.ae/catalogues/42/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/bufet/ilsa.pdf` | 206 | application/pdf | bytes 0-1023/3588004 | PDF подтверждён |
| `https://grhs.ae/catalogues/43/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/bufet/abert.pdf` | 206 | application/pdf | bytes 0-1023/9269469 | PDF подтверждён |
| `https://grhs.ae/catalogues/44/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/poolware/ilsa.pdf` | 206 | application/pdf | bytes 0-1023/3156529 | PDF подтверждён |
| `https://grhs.ae/catalogues/45/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/poolware/viejo-valle.pdf` | 206 | application/pdf | bytes 0-1023/16117894 | PDF подтверждён |
| `https://grhs.ae/catalogues/46/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/storage/catalogues/compressed/catalogs-compressed/poolware/glass-forever.pdf` | 206 | application/pdf | bytes 0-1023/1974744 | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/barware/aoyama/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/barware/birdy/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/barware/gabriel-glas/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/barware/ishizuka/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/cutlery/eme/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/cutlery/sabre/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/glassware/aoyama/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/glassware/glassmade/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/glassware/sklarna/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/glassware/solidwater/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/glassware/yoshinuma/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/kitchenware/casalinghi/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/kitchenware/ilsa/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/poolware/glassforever/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/tableware/arita-japan/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/tableware/gien/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/tableware/kenai/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/tableware/le-coq/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/tableware/soul/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/wood/trud/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
| `https://grhs.ae/brand-catalogues/wood/woodeez/download` | 200 | application/pdf | не подтверждён | PDF подтверждён |
