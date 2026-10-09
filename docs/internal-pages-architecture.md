# GRHS — архитектура внутренних страниц

Дата анализа: 2026-10-08. Это проектное предложение по исходникам `old-theme/`, двум существующим документам аудита/миграции и текущему Laravel-проекту. Старую тему, код сайта, маршруты, данные и файлы в рамках анализа не изменяли. PDF не копировались и не перемещались.

## Краткий вывод

- В `old-theme/pages/` находятся 47 файловых деклараций страниц: главная и 46 уникальных значений `url`. Путь `/glassware/hirota-glass` задан дважды.
- Основные шаблоны данных: категория-витрина, брендовая страница, библиотека каталогов, информационная страница контактов. `/ucello` — отдельная самостоятельная страница бренда/коллекции, а `/tableware/hibino` представляет тот же брендовый контент в другой точке URL-карты.
- Набор ресурсов не требует обязательных many-to-many таблиц между Category, Brand и Catalogue. На видимых данных каталог имеет одну категорию и один бренд; бренд может иметь несколько публичных URL и несколько категорий, поэтому достаточно связки Brand → Category (многие бренды к одной категории — при подтверждении единственной категории у бренда) либо pivot только если бизнес-правило подтвердит множественную классификацию. Каталогам достаточно `category_id` и nullable `brand_id`.
- Существующие URL являются частью контракта. Их следует хранить явно в сущностях, не строить автоматически из slug и не заменять шаблонными `/brands/{slug}` / `/categories/{slug}`.
- В дереве темы найдено 123 PDF (аудит: около 2.34 GiB), включая две версии каталожных файлов в библиотеке, отдельные файлы для брендовых страниц, дубликаты и исторические пути. В БД хранятся только относительные storage paths и метаданные.
- До разработки необходимо согласовать два конфликтующих Hirota URL-источника, структуру связи Brand–Category, роль legacy URL для каталогов и перенос двух информационных/специальных страниц.

## 1. Источники и границы анализа

Изучены `docs/legacy-site-audit.md`, `docs/homepage-migration-plan.md`, все страницы в `old-theme/pages/`, общие layouts/partials, ссылки на изображения и PDF, структура PDF в `old-theme/assets/`, Laravel `routes/web.php`, `app/Providers/Filament/AdminPanelProvider.php`, `composer.json` и текущая структура `app/`, `resources/`, `database/`.

Текущий Laravel использует Laravel 13 и Filament 5 согласно `composer.json`. В `routes/web.php` пока объявлен только `/`, который выводит готовую главную. Файлы `docs/` уже содержат изменения пользователя; их и прочие существующие изменения не перезаписывали.

Анализ темы не подтверждает реальное поведение production October CMS, состояние Search Console/аналитики, базу October и правила сервера. Все выводы о доступности файлов основаны на исходниках и присутствующих локальных файлах.

## 2. Инвентаризация и классификация страниц

### 2.1 Категории

Файлы категорий: `barware.htm`, `cutlery.htm`, `glassware.htm`, `kitchenware.htm`, `lighting.htm`, `outdoor.htm`, `poolware.htm`, `tableware.htm`, `wood.htm`. Это 9 категорийных страниц.

| URL | Заголовок | Карточки/внутренние ссылки на бренды | Основное изображение | SEO / особенности |
|---|---|---|---|---|
| `/barware` | Barware | Birdy, Ishizuka, Aoyama, Gabriel Glass, Cocktail Kingdom; карточка Ishizuka повторяется с тем же URL | `assets/images/header-banner/barware.jpg`, карточки из `assets/images/barware/`, `new-pages/coctail_kingdom/` | Есть meta description, canonical с `/`; источник дублирует карточку Ishizuka |
| `/cutlery` | Cutlery | EME, Sabre | `assets/images/header-banner/cutlery.jpg` и карточки брендов | Есть description и canonical с завершающим `/` |
| `/glassware` | Glassware | Aoyama, Glassmade, Hirota Glass, Ishizuka, Sklarna, Solidwater, Yoshinuma | `assets/images/header-banner/glassware.jpg`, `assets/images/glassware/` и брендовые assets | Есть description и canonical с завершающим `/`; ссылки Ishizuka в исходнике требуют сверки slash |
| `/kitchenware` | Kitchenware | Casalinghi, ILSA | `assets/images/header-banner/kitchenware.jpg` | Есть description; canonical в front matter отсутствует |
| `/lighting` | Lighting | Vetvi | `assets/images/header-banner/lighting.jpg` | Meta description и canonical отсутствуют |
| `/outdoor` | Outdoor | KaveHome | `assets/images/header-banner/outdoor.jpg` | Meta description и canonical отсутствуют |
| `/poolware` | Poolware | Glass Forever | `assets/images/header-banner/poolware.jpg` | Есть description; canonical отсутствует |
| `/tableware` | Tableware | Arita, Bitossi Home, Gien, Hibino/Uccello, Kenai, Kurieto, Le Coq, Miyama, Molleni, Resobject, Rina Menardi, Soul Studio | `assets/images/header-banner/tableware.webp`, карточки `assets/images/tableware/` | Есть description; canonical отсутствует; в карточках есть изображения брендов/коллекций |
| `/wood` | Wood | TRUD Makers, Woodeez | `assets/images/header-banner/wood.jpg` | Есть description; canonical с завершающим `/` |

Категорийный шаблон обычно состоит из hero с названием и фоновой фотографией, затем сетки карточек (логотип/название поверх изображения), ведущих на брендовые URL. Страница не обязана иметь отдельное длинное описание. Карточки и их порядок заданы HTML. Категории не выводят PDF непосредственно.

Отдельный `/tableware/hibino` имеет frontmatter title `Uccello`, hero/галерею и description. `/ucello` также имеет страницу бренда Uccello. Это может быть дублированная/устаревшая брендовая страница, а не ещё одна категория.

### 2.2 Брендовые страницы

В дереве 35 деклараций брендовых URL (с учётом двух конфликтующих Hirota URL), включая страницы с контентом, изображениями и часто кнопкой PDF. Все URL в таблице — точные старые публичные пути. Сегмент категории у брендового адреса важен и сохраняется.

| URL | Источник страницы / отображаемый бренд | Иллюстрации в `old-theme/assets/images/` | PDF или особенность |
|---|---|---|---|
| `/barware/aoyama` | `barware/aoyama.htm` — Aoyama | `images/barware/aoyama/` + общие | `catalogs/new-catalogs/AOYAMA GLASS BARWARE.pdf` |
| `/barware/birdy` | `barware/birdy.htm` — Birdy | `images/barware/birdy/` + общие | `catalogs/new-catalogs/Birdy Barware 3.pdf` |
| `/barware/coctail-kingdom` | `barware/coctail_kingdom.htm` — Cocktail Kingdom; frontmatter title ошибочно `Birdy` | `images/new-pages/coctail_kingdom/` и общие | В кнопке привязан тот же Birdy PDF, что требует проверки |
| `/barware/gabriel-glas` | `barware/gabriel.htm` — Gabriel Glass | `images/barware/gabriel-glass/` + общие | `catalogs/glassware/gabriel-glas.pdf`; slug `glas` сохраняется |
| `/barware/ishizuka` | `barware/ishizuka.htm` — Ishizuka Glass / ADERIA | `images/barware/ishizuka/` | `catalogs/new-catalogs/ISHIZUKI BARWARE JAPAN.pdf` (написание ISHIZUKI) |
| `/cutlery/eme` | `cutlery/eme.htm` — EME | `images/cutlery/eme/` | `catalogs/cutlery/eme.pdf` |
| `/cutlery/sabre` | `cutlery/sabre.htm` — Sabre | `images/cutlery/sabre/` | `catalogs/cutlery/Sabre_Catalogue_2024.pdf` |
| `/glassware/aoyama` | `glassware/aoyama.htm` — Aoyama | `images/glassware/aoyama/` | `catalogs/new-catalogs/AOYAMA GLASS GLASSWARE.pdf` |
| `/glassware/glassmade` | `glassware/glassmade.htm` — Made Glass | `images/glassware/glassmade/` | `catalogs/glassware/made-glass.pdf` |
| `/glassware/hirota-glass` | `glassware/hirota-glass.htm` — Hirota Glass | `images/hirota/` (webp) | Ссылка `/themes/goldenratio/assets/images/catalog-block/catalogs/glassware/hirota-glass.pdf` |
| `/glassware/ishizuka/` | `glassware/ishizuka.htm` — Ishizuka Glass | `images/new-pages/ishizuka_glass/` | Две кнопки ведут на `#`, файл в странице не указан |
| `/glassware/sklarna` | `glassware/sklarna.htm` — Sklarna | `images/glassware/sklarna/` | `catalogs/glassware/sklarna.pdf` |
| `/glassware/solidwater` | `glassware/solid.htm` — Solid Water | `images/glassware/solid/` | `catalogs/glassware/solid-water.pdf` |
| `/glassware/yoshinuma` | `glassware/yoshinyma.htm` — Yoshinuma | `images/glassware/yoshinuma/` | `catalogs/new-catalogs/YOSHINUMA.pdf` |
| `/glassware/hirota-glass` | `hirota-glass.htm` — второй Hirota источник | Данные/галерея отличаются; использует Hirota assets | Ссылка `/themes/goldenratio/assets/catalogs/glassware/hirota.pdf` |
| `/kitchenware/casalinghi` | `kitchenware/casalinghi.htm` — Casalinghi | `images/kitchenware/casalinghi/` | `catalogs/new-catalogs/Casalinghi.pdf` |
| `/kitchenware/ilsa` | `kitchenware/ilsa.htm` — ILSA | `images/kitchenware/ilsa/` | `catalogs/new-catalogs/ILSA.pdf` |
| `/lighting/vetvi` | `lightning/vetvi.htm` — Vetvi | `images/lighting/vetvi/` | `catalogs/lighting/vetvi.pdf`; каталог папки называется `lightning`, URL — `lighting` |
| `/outdoor/kavehome` | `outdoor/kavehome.htm` — KaveHome | `images/outdoor/kavehome/` | PDF не найден в странице |
| `/poolware/glassforever` | `poolware/glassforever.htm` — Glass Forever | `images/poolware/glassforever/` | `catalogs/new-catalogs/Everglass.pdf` |
| `/tableware/arita-japan` | `tableware/arita.htm` — Arita / Arita 1616 | `images/tableware/arita/` | `catalogs/tableware/ar-arita.pdf` |
| `/tableware/bitossi-home` | `tableware/bitossi-home.htm` — Bitossi Home | `images/tableware/bitossi-home/` | PDF-кнопка не обнаружена |
| `/tableware/gien` | `tableware/gien.htm` — Gien | `images/tableware/gien/` | `catalogs/new-catalogs/Gien.pdf` |
| `/tableware/hibino` | `tableware/hibino.htm` — отображаемое Uccello | `images/tableware/hibino/` | PDF-кнопка не обнаружена; смысл URL/title расходится |
| `/tableware/kenai` | `tableware/kenai.htm` — Kenai | `images/tableware/kenai/` | `catalogs/tableware/kenai.pdf` |
| `/tableware/kurieto` | `tableware/kurieto.htm` — Kurieto | `images/tableware/kurieto/` | Ссылка `/themes/goldenratio/assets/catalogs/tableware/kurieto.pdf` |
| `/tableware/le-coq` | `tableware/le-coq.htm` — Le Coq | `images/tableware/le-coq/` | `catalogs/new-catalogs/LE-COQ.pdf` |
| `/tableware/miyama` | `tableware/miyama.htm` — Miyama | `images/tableware/miyama/` | PDF-кнопка не обнаружена |
| `/tableware/molleni` | `tableware/molleni.htm` — Molleni | `images/tableware/molleni/` | Ссылка `/themes/goldenratio/assets/catalogs/tableware/molleni.pdf` |
| `/tableware/resobject` | `tableware/resobject.htm` — Resobject | `images/tableware/resobject/` | `catalogs/tableware/res-objects.pdf` |
| `/tableware/rinamenardi` | `tableware/rhina.htm` — Rina Menardi | `images/tableware/rhina/` | `catalogs/tableware/rina-menardi.pdf` |
| `/tableware/soul` | `tableware/soul-studio.htm` — Soul Studio | `images/tableware/soul-studio/` | `catalogs/tableware/soul.pdf` |
| `/ucello` | `ucello.htm` — Uccello | `images/ucello/` | PDF-кнопка не обнаружена |
| `/wood/trud` | `wood/trud.htm` — TRUD Makers | `images/wood/trud/` | `catalogs/wood/trud.pdf` |
| `/wood/woodeez` | `wood/woodeez.htm` — Woodeez | `images/wood/woodeez/` | `catalogs/wood/woodeez.pdf` |

Общая композиция бренда: hero/banner (логотип или H1 поверх cover), чередующиеся информационные секции с текстом и фотографиями, в некоторых случаях галерея, затем кнопка загрузки каталога. Набор секций и порядок индивидуальны, например Le Coq содержит четыре фото, а Ishizuka — несколько исторических абзацев. Общий шаблон должен поддерживать последовательность секций, но фиксированная модель одного `description` не покроет все страницы. Редакторские блоки допустимы только если редакторы должны менять эти истории; для первоначального переноса может использоваться общий Blade с данными секций или статическими структурированными данными.

SEO: в старых front matter у всех страниц присутствуют `meta_title`, `robot_index=index`, `robot_follow=follow`; `meta_description` имеется у 32 из 47 деклараций, `canonical_url` — у 12. Canonical иногда отличается slash от URL. У части страниц задаётся `og_img`, а layouts печатают title/description/author и Open Graph image. Дополнительный October-компонент `SeoCmsPage` может выводить другие метатеги, но без October runtime это неизвестно. На переносе следует сохранить title, description, canonical, robots и OG image как поля сущностей/страниц и сравнить итоговый HTML.

### 2.3 Библиотека каталогов

URL `/catalogues`, файл `old-theme/pages/catalogue.htm`, layout `default_black`.

Структура: заголовок «Catalogue Library», текстовый поиск, фильтры Category, Concept, Brand и повторяющаяся карточка каталога. В карточке есть превью/логотип и ссылки «download» и «view». Источник содержит 46 карточек с `data-category`, `data-brand`, `data-concept`, `data-large`, изображениями и двумя PDF-ссылками — исходная и compressed. Присутствие фильтров в разметке подтверждено, но аудит не нашёл клиентскую фильтрацию в `assets/js/main.js`; фактическая работоспособность требует runtime-проверки.

Категории фильтра каталога включают `tableware`, `barware`, `cutlery`, `steak-knives`, `wood`, `kitchen-accessories`, `asian-concepts`, `bufet`, `poolware`. Concept — значения из фильтра страницы (Italian, French, Spanish, Greek, Japanese, Pan-Asian, Bar и др.). Brand имеет собственные идентификаторы, местами отличающиеся от slug страниц. Для изображений карточек используются `assets/images/catalog-block/<category>/<brand>.*`; PDF лежат в парных деревьях `assets/images/catalog-block/catalogs/` и `catalogs-compressed/`.

SEO: title/description заданы; canonical — `https://grhs.ae/catalogues` без завершающего slash. OG image указывает на общий `header-banner/main.jpg`. Внутренние связи здесь преимущественно к PDF; ссылка на брендовые страницы не является обязательной для каждой карточки.

### 2.4 Информационные и специальные страницы

| URL | Тип и контент | Медиа / интеграции | SEO и внутренние ссылки |
|---|---|---|---|
| `/contacts` | Контакты, форма, контактные данные, карта | `assets/images/contacts.jpg`, иконки футера, Mapbox GL JS/CSS и публичный access token в inline JS | Title есть; description нет; canonical содержит slash. Ссылки `tel:`, `mailto:`, Instagram. Форма выглядит статической и обработчик в теме не подтверждён |
| `/ucello` | Специальная самостоятельная брендовая страница | `assets/images/ucello/` | Есть description и canonical. Связана по смыслу с `/tableware/hibino`, поэтому статус обеих страниц надо выяснить |
| `/tableware/hibino` | Страница в категории Tableware, фактически размеченная как Uccello | `assets/images/tableware/hibino/` | Есть description и canonical. Несоответствие URL `hibino` отображаемому бренду Uccello |

`/` — готовая главная (не часть проектируемых внутренних шаблонов, не менять); `/catalogues` — отдельная библиотека, хотя в меню она называется каталогами. Legal notice, Terms, Privacy, Cookies представлены ссылками `#` в footer, отдельных page URL/файлов не обнаружено. Не создавать для них Page-записи до решения о необходимости реальных документов.

## 3. Предлагаемая модель данных

Цель — минимально поддержать контент, точные старые адреса, SEO, карточки и загрузки. URL является отдельным полем и не выводится из slug.

### `categories`

- `id`, `name`, `slug` (уникальный административный идентификатор, не обязательно соответствует URL).
- `public_path` (уникальный путь с ведущим `/`, хранить без завершающего slash; для исторического slash исключение/алиас на маршрутизации).
- `summary`/`description` nullable; `hero_image_path` nullable; `sort_order` unsigned.
- SEO: `meta_title`, `meta_description`, `canonical_url`, `og_image_path`, `robots_index`, `robots_follow`.
- `is_published`, timestamps.
- Иерархия через nullable `parent_id` только при появлении реальных подкатегорий в структуре данных; сейчас URL-вложенность отражает категорию бренда, а не вложенные категории.

Уникальность: `slug`; `public_path`. Не задавать уникальность по `name`.

### `brands`

- `id`, `name`, `slug` (уникальный внутренний slug), `display_name` nullable.
- Один или более публичных адресов должны сохраняться отдельно от этой таблицы: см. ниже выбор `brand_routes`.
- `logo_path`, `cover_image_path` nullable.
- `short_description`, `description` nullable.
- Для брендовой истории — отдельные `brand_sections` не вводить до подтверждения необходимости редакторского управления. Если нужно управлять историей через Filament, минимальная таблица секций: `id`, `brand_id`, `sort_order`, `heading` nullable, `body`, `image_path` nullable, `image_alt` nullable, `image_position` enum/string. Это обосновывается разнотипным повторяющимся контентом, но усложняет Filament и импорт.
- SEO поля как у Category; `sort_order`, `is_published`, timestamps.

### `brand_routes` (рекомендуется из-за фактической структуры URL)

Таблица нужна, если один Brand может иметь более одного старого URL: Uccello (`/ucello`, `/tableware/hibino`) и потенциальные категории Aoyama/Ishizuka с раздельными страницами. Поля: `id`, `brand_id`, `category_id` nullable, `path` unique, `is_primary`, `is_published`, `sort_order`, timestamps. SEO поля — на маршруте, если заголовок/canonical различаются у двух URL; иначе SEO — на Brand. Для минимального старта допустимо одно поле `public_path` прямо в brands, но тогда несколько URL на один бренд нормально не выразить.

### `catalogues`

- `id`, `title` (текст карточки), `slug` (внутренний уникальный идентификатор), `brand_id` nullable, `category_id` nullable.
- `cover_image_path` nullable, `original_file_path`, `web_file_path` nullable; хранить только относительные пути на диске Laravel. `web_file_path` обозначает пригодный для браузерного просмотра вариант, не требует наличия второй версии.
- `original_filename`, `web_filename` nullable; `file_size_bytes`, `mime_type`, `published_at` nullable; `sort_order`, `is_published`, timestamps.
- В каталоге нет необходимости хранить PDF bytes в SQL. При неизменяемом исходнике можно хранить метаданные двух вариантов в самой записи, а не создавать отдельные сущности версий.
- Legacy URL для файла — не свойство каталога по умолчанию: одна физическая PDF-версия может иметь несколько старых URL. См. `legacy_file_paths` ниже.

### `pages` — только по явной потребности

Не вводить универсальный конструктор CMS-страниц только ради `/contacts`: достаточно отдельного Blade и конфигурации/небольшой сущности ContactPageSettings, если контакты должны редактироваться. Если требуется Filament редактирование произвольных статических страниц (контакты и будущие legal pages), создать `pages` с `title`, unique `slug`, `public_path`, `body` или JSON-блоками, SEO полями, `is_published`, `published_at`; однако универсальный HTML/JSON builder выходит за минимальную потребность текущего набора.

### `legacy_file_paths` — совместимость прямых PDF URL

Для надёжного старого URL-контракта нужна малая таблица алиасов PDF: `id`, `catalogue_id`, `legacy_path` unique (например `/themes/goldenratio/assets/catalogs/tableware/kurieto.pdf`), timestamps. Запрос этого адреса контроллер/маршрут находит в таблице и отдаёт текущий storage-файл. Это не новый контентный объект, а карта обратной совместимости. Если сервер/Nginx настроен обслуживать legacy URLs напрямую из прежнего дерева без Laravel, можно сохранить файлы на старом публичном пути вместо alias table, но это противоречит целевому управлению в Laravel и требует подтверждения инфраструктуры.

### Связи и решение про many-to-many

| Связь | Предложение | Основание |
|---|---|---|
| Category ↔ Brand | Начать с `brand_routes.category_id` (несколько маршрутов/классификаций на бренд); простой вариант — `brands.category_id`, если подтверждено ровно одно размещение. Pivot `brand_category` не добавлять заранее | В старом сайте Aoyama и Ishizuka фигурируют в двух разделах. Это доказывает много URL/представлений, но не доказывает, что один Brand должен выводиться в одной общей динамической выборке через две категории. Категория маршрута естественно живёт у маршрута бренда |
| Brand → Catalogue | Один-ко-многим, `catalogues.brand_id` nullable | Каждая карточка обозначает один бренд; повторы бренда — разные документы/представления, не M:N |
| Category → Catalogue | Один-ко-многим, `catalogues.category_id` nullable | Фильтр имеет одну категорию на карточку. Необходимость нескольких категорий на один PDF не доказана |
| Category ↔ Catalogue / Brand ↔ Catalogue | Не использовать pivot в первом этапе | В источнике у каждого каталога одно значение category и brand. При подтверждении multi-category допускается pivot позднее |
| Concept ↔ Catalogue | Отложить | UI старой страницы содержит concept фильтр, но в текущем снимке не установлено, что фильтр рабочий, а полный набор меток/связей не подтверждён. Если этот фильтр нужен в первой версии — добавить `concepts` и `catalogue_concept` (реальный M:N, поскольку каталог может относиться к нескольким concept) |

## 4. URL и Laravel routing

Маршруты должны быть явными/данными с самым конкретным путём в приоритете. Простой wildcard `/{category}/{brand?}` может конфликтовать с `/contacts`, `/catalogues`, статическими категориями и `/glassware/hirota-glass`. Предпочтительно зарегистрировать таблицу категорий/brand_routes и явные системные маршруты; при чтении динамической страницы искать точный `public_path` из активных записей. Публичные адреса не выводить из slug.

| Старый URL | Тип | Предлагаемый Laravel route | Источник данных | Возможный конфликт / решение |
|---|---|---|---|---|
| `/` | Главная | Существующий `home` | Готовая homepage view | Существующий маршрут оставить |
| `/catalogues` | Каталоги | `catalogues.index` точный путь | Category + Brand + Catalogue | Зарезервированный системный URL; до динамических маршрутов |
| `/contacts` | Контакты | `contacts` точный путь | Пока статический Blade / будущая Pages запись | Зарезервировать до catch-all |
| `/barware` | Категория | `categories.show` разрешение по `public_path` | Category | Не конкурирует при точном пути |
| `/barware/aoyama` | Бренд | `brands.show` точный `brand_routes.path` | Brand + brand route | Вложенность не повод менять URL |
| `/barware/birdy` | Бренд | То же | Brand + brand route | — |
| `/barware/coctail-kingdom` | Бренд | То же | Brand + brand route | Title/PDF в legacy ошибочны; импорт не должен считать их корректными без проверки |
| `/barware/gabriel-glas` | Бренд | То же | Brand + brand route | Сохранить `glas` в path |
| `/barware/ishizuka` | Бренд | То же | Brand + brand route | Отдельно от glassware варианта |
| `/cutlery` | Категория | `categories.show` | Category | — |
| `/cutlery/eme` | Бренд | `brands.show` | Brand + brand route | — |
| `/cutlery/sabre` | Бренд | `brands.show` | Brand + brand route | — |
| `/glassware` | Категория | `categories.show` | Category | — |
| `/glassware/aoyama` | Бренд | `brands.show` | Brand + brand route | Один Brand может иметь маршрут и в Barware |
| `/glassware/glassmade` | Бренд | `brands.show` | Brand + brand route | — |
| `/glassware/hirota-glass` | Бренд | `brands.show` с одним canonical record | Brand + brand route | Два исходных файла объявляют один путь; определить единственную версию и PDF |
| `/glassware/ishizuka/` | Бренд | Нормализованный `brands.show` + разрешение завершающего slash | Brand + brand route | Исторический URL содержит slash; сохранить доступность обоих вариантов, canonical зафиксировать после проверки старого сайта |
| `/glassware/sklarna` | Бренд | `brands.show` | Brand + brand route | — |
| `/glassware/solidwater` | Бренд | `brands.show` | Brand + brand route | Slug отличается от `solid-water.pdf` |
| `/glassware/yoshinuma` | Бренд | `brands.show` | Brand + brand route | Источник filename `yoshinyma.htm` — опечатка только файла, URL сохранить |
| `/kitchenware` | Категория | `categories.show` | Category | — |
| `/kitchenware/casalinghi` | Бренд | `brands.show` | Brand + brand route | — |
| `/kitchenware/ilsa` | Бренд | `brands.show` | Brand + brand route | — |
| `/lighting` | Категория | `categories.show` | Category | — |
| `/lighting/vetvi` | Бренд | `brands.show` | Brand + brand route | Файл лежит в `pages/lightning/`, URL — `/lighting/` |
| `/outdoor` | Категория | `categories.show` | Category | — |
| `/outdoor/kavehome` | Бренд | `brands.show` | Brand + brand route | — |
| `/poolware` | Категория | `categories.show` | Category | — |
| `/poolware/glassforever` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware` | Категория | `categories.show` | Category | — |
| `/tableware/arita-japan` | Бренд | `brands.show` | Brand + brand route | Filename `arita.htm`; сохранить alias |
| `/tableware/bitossi-home` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware/gien` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware/hibino` | Бренд / спорный alias | `brands.show` только после решения | Brand + brand route | Старый slug Hibino рендерит Uccello; может быть самостоятельный бренд/страница |
| `/tableware/kenai` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware/kurieto` | Бренд | `brands.show` | Brand + brand route | Hard-coded PDF URL находится в `/assets/catalogs/` |
| `/tableware/le-coq` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware/miyama` | Бренд | `brands.show` | Brand + brand route | — |
| `/tableware/molleni` | Бренд | `brands.show` | Brand + brand route | Hard-coded PDF URL находится в `/assets/catalogs/` |
| `/tableware/resobject` | Бренд | `brands.show` | Brand + brand route | URL отличается от Res Objects filename |
| `/tableware/rinamenardi` | Бренд | `brands.show` | Brand + brand route | Сохранить слитный URL, filename `rhina.htm` |
| `/tableware/soul` | Бренд | `brands.show` | Brand + brand route | URL короче Soul Studio |
| `/ucello` | Бренд/специальная страница | `brands.show` точное значение из brand_routes | Brand + brand route | Возможная дублирующая страница к `/tableware/hibino` |
| `/wood` | Категория | `categories.show` | Category | — |
| `/wood/trud` | Бренд | `brands.show` | Brand + brand route | — |
| `/wood/woodeez` | Бренд | `brands.show` | Brand + brand route | — |

### Slash и canonical

Laravel/веб-сервер часто принимают URL со slash-нормализацией, но в данном проекте нужно проверить фактическое поведение reverse proxy. Рекомендация: выбрать один canonical вариант для каждого пути по live crawl/analytics; принимать исторический вариант со slash и отвечать без лишнего redirect chain. По крайней мере `/glassware/ishizuka/` задан именно так, а canonical некоторых других страниц расходится с URL front matter. Не менять canonical автоматически по `public_path` до сверки всех 12 старых canonical значений.

### Старые URL PDF в `/themes/goldenratio/assets/`

В теме прямые ссылки встречаются в нескольких ветках:

- `/themes/goldenratio/assets/images/catalog-block/catalogs/<category>/<file>.pdf`
- `/themes/goldenratio/assets/images/catalog-block/catalogs-compressed/<category>/<file>.pdf`
- `/themes/goldenratio/assets/catalogs/<category>/<file>.pdf`
- ссылки на `assets/catalogs/...` генерируются `|theme` и при October deployment также становились адресами под `/themes/goldenratio/assets/...`.

Не создавать один общий `/storage/...` endpoint, предполагая, что достаточно перенести файл: старые прямые URL должны оставаться рабочими. Предлагается сохранить старый path как alias к текущему Catalogue-файлу. Если несколько путей ведут на один и тот же байтовый файл, для них создаются отдельные alias-записи. Точные ответы (200/redirect, inline/attachment, Range requests) зафиксировать при реализации. После миграции каждую старую ссылку следует проверять GET/HEAD и byte/hash-эквивалентность.

## 5. PDF: привязка, повторное использование, хранение

### Наблюдения

- Всего в теме 123 PDF. Дерево `assets/images/catalog-block/catalogs/` содержит библиотечные оригиналы, `catalogs-compressed/` — альтернативные web-версии тех же карточек. В `assets/catalogs/` лежат документы, на которые ссылаются страницы брендов.
- Каталожная страница задаёт по две ссылки на карточку: обычный PDF и compressed PDF. Оба пути имеют отдельные URL, хотя название и карточка одни.
- Аудит по SHA-256 обнаружил 23 группы идентичных файлов, 24 избыточные копии суммарно; из PDF повторяются `hirota.pdf`, `kurieto.pdf`, `molleni.pdf` между `assets/catalogs/` и `assets/images/catalog-block/catalogs/`. Это не основание удалять алиасы.
- В двух папках каталожной библиотеки присутствуют одноимённые пары примерно 46 карточек. По именам нельзя установить полную эквивалентность; аудит подтверждает также крупные файлы, поэтому compressed-вариант сам по себе не гарантирует малый размер.
- Брендовые страницы нередко ссылаются на иной PDF, чем карточка каталога того же бренда. У Cocktail Kingdom ошибочно подключён Birdy PDF. Ishizuka Glass page имеет `#` вместо документа. Эти привязки требуют контентной сверки.

### Laravel storage и Filament

Хранить файлы на filesystem disk (`storage/app/public` или настроенном приватном disk согласно требованиям доступа); в таблицах — только disk/path, имя загрузки, MIME, размер и даты. Не хранить байты в SQL и не складывать PDF в `public/` как единственный управляемый вариант. Нужна оценка upload limit, backup и размера диска: весь legacy архив около 2.34 GiB.

В Filament Catalogue form поля `original_file_path` и `web_file_path` должны быть отдельными загрузками, с ограничением PDF MIME/extension, отображением оригинального имени/размера и возможностью заменить конкретную версию. При замене файла необходимо удалять старый физический файл только если он не разделяется другими записями/путями; безопасная первая политика — сохранять прежние файлы до подтверждения алиасов и выполнять замену в транзакционно управляемом lifecycle. Удаление записи не должно тихо ломать legacy alias.

Для просмотра/скачивания в браузере public path может быть закрыт контроллером, который отдаёт файл inline, а download-link — тем же endpoint с attachment. Для больших PDF учитывать HTTP Range, reverse proxy, timeouts и cache headers. Не требуется создавать preview images пока дизайн и performance requirements не подтверждены.

## 6. Filament 5: ресурсы и администрирование

Существующий `AdminPanelProvider` уже включает discovery для `app/Filament/Resources`; отдельные зависимости для ресурсов не нужны.

### Categories

- Основное: name, slug, public path (точный legacy route), hero image, description, sort order, publication status.
- SEO: meta title, meta description, canonical URL, OG image, index/follow.
- Связи: просмотр brand routes в категории; в первом варианте маршруты брендов редактировать в Brand resource либо relation manager, но не дублировать источник истины.
- Image upload на public disk с ограничением изображения, alt text при необходимости.
- Publish/hide через `is_published`; сортировка `sort_order`; soft delete не нужен пока не спроектирована политика сохранения старых URL.

### Brands

- Основное: name, display name, slug, logo, cover, краткое описание, статус, sort order.
- Блоки brand story — repeater/Relation Manager к `brand_sections`, если согласовано управление контентом; иначе импортировать тексты/галереи как фиксированные данные и не расширять форму.
- Brand routes: `path`, category, primary flag, per-route SEO/canonical только когда различается.
- SEO: meta title/description, canonical, OG image, robots. При нескольких URL на бренд нужен ясный выбор canonical и SEO override на маршруте.
- Изображения загружаются на выбранный disk; порядок истории контролируется `sort_order` секций.

### Catalogues

- Основное: title, category, brand, cover image, sort order, publication status/published date.
- Upload original PDF и optional web/preview PDF — разные поля; в списке показывать file name, MIME/size, наличие legacy aliases.
- Фильтры/Concept поля добавить после подтверждения реальной работы фильтра; если нужно — отдельный Concepts relation manager и pivot, потому что каталог может иметь несколько тематик.
- SEO не требуется для каждого PDF каталога; SEO поля принадлежат `/catalogues`, категориям и страницам брендов. Если у отдельной записи появится landing URL, тогда это отдельная Page/route с SEO, не просто метаданные файла.
- Управление alias path не должно быть скрытым side effect от переименования файла. Отдельный read-only/relation section к legacy alias map предпочтительнее ручного ввода без валидации.

### Pages

Пока не создавать ресурс Pages автоматически. Контакты имеют специализированную структуру, карту и форму, а legal URL отсутствуют. Если потребуется полноценное управление статическими текстовыми страницами и legal content, согласовать Page-модель и отдельный ресурс до реализации. Любой Page route должен проходить проверку конфликтов с зарезервированными URLs и быть уникальным.

## 7. Пошаговый план реализации

1. **Закрыть содержательные решения и зафиксировать URL manifest.** Сверить live URL/canonical и обе Hirota версии, связь Uccello/Hibino, дублирующиеся карточки, PDF кнопки `#` и ошибки Cocktail Kingdom. Зафиксировать каждый legacy PDF path и его фактический файл/хэш. Это исключит импорт противоречивых данных.
2. **Миграции и модели.** Создать Categories, Brands, Brand routes (либо подтвердить один path на бренд), Catalogues и alias paths. Включать `brand_sections`/Concept pivots только при подтверждении редакторских/фильтровых требований. Добавить уникальные ограничения для paths/slugs.
3. **Импорт данных старого сайта.** Одноразовый importer/seeder из проверенного manifest: названия, slugs, сохранённые URL, изображения, тексты/секции, SEO, категории/брендовые связи, каталоги и aliases. Проверить подсчёт записей, не переносить сам PDF в рамках анализа; перенос произойдёт только после отдельного решения по storage и проверки hashes.
4. **Совместимость публичных URL и PDF.** Реализовать явные system routes и path resolver без wildcard-конфликтов; поддержать canonical/trailing slash; подключить legacy PDF aliases к текущему storage path. Это целесообразно до выдачи внутренних страниц, чтобы любой новый шаблон уже проверялся по финальным адресам.
5. **Общие Blade-шаблоны внутренних страниц.** Сначала category index/show и brand page composition; затем `/catalogues` с каталогами/поиском/фильтрами только по подтверждённой спецификации; специализированный contacts view остаётся отдельным шаблоном. Учитывать готовый общий layout главной, не менять саму homepage.
6. **Filament Resources.** Реализовать Categories, Brands, Catalogues и согласованные relationship managers. Настроить валидацию путей, публикации, изображения, PDF, замены файлов и sort order. Pages — только если пользователь согласует.
7. **Тестирование и сверка.** Проверить каждый из 46 уникальных публичных URL, slash/canonical/robots/meta, ссылки категории↔бренд↔каталог, все legacy PDF paths и отдачу файлов (включая Range при необходимости), публикацию/скрытие, формы Filament, отсутствующие assets и 404. Сверить статус-коды/редиректы и HTML с исходной картой и, когда доступен, production crawl.

Порядок отличается от простой последовательности «модели → шаблоны → Filament → совместимость»: URL/PDF совместимость надо спроектировать и включить до завершения шаблонов, иначе есть риск заново менять ссылки/маршруты после вёрстки. Filament остаётся после данных и публичного контракта.

## 8. Открытые вопросы, противоречия и решения к согласованию

### Требуют согласования до начала разработки

1. **Два источника `/glassware/hirota-glass`.** `pages/glassware/hirota-glass.htm` ссылается на `images/catalog-block/catalogs/glassware/hirota-glass.pdf`; `pages/hirota-glass.htm` — на `assets/catalogs/glassware/hirota.pdf`. Выбрать контент, описание и действующий PDF. Путь страницы остаётся один.
2. **Модель Brand URL.** Подтвердить хранение нескольких `brand_routes` на один Brand (рекомендуется из-за `/ucello` и `/tableware/hibino`, категорийных путей брендов) или правило, что это разные Brand/Page сущности. Вариант определяет схему и canonical.
3. **Категория бренда/каталога.** Старый каталог показывает одну категорию на карточку, но бренды встречаются в нескольких разделах. Подтвердить категорию маршрута для бренда и требуется ли каталогу несколько категорий. Рекомендуемая начальная схема — route-to-category, catalogue-to-single-category без pivot.
4. **Uccello и Hibino.** `/tableware/hibino` размечен title/контентом Uccello; `/ucello` тоже существует. Установить, должны ли оба URL отображать одну страницу, разный контент или один стать redirect/canonical alias.
5. **Файлы PDF и старые прямые адреса.** Подтвердить, что при публикации Laravel старые `/themes/goldenratio/assets/...` должны обслуживаться приложением/веб-сервером и не останутся на legacy host. Согласовать возможность временно хранить алиас-маршруты и оригинальный плюс web PDF. Нужны целевые лимиты upload/storage/backup.
6. **Редактирование историй брендов.** Решить, должны ли маркетологи править блоки текста/картинок через Filament. Если да — добавить управляемые `brand_sections`; если контент импортируется разово и правится разработчиком, начать без этой таблицы.
7. **Concept-фильтр каталогов.** Подтвердить, должен ли работать в Laravel с множественными concept на каталоге. Тема предоставляет список вариантов и data attributes, но аудит не подтвердил работоспособность фильтра.
8. **Contacts и legal страницы.** Подтвердить, остаётся ли `/contacts` статической специализированной страницей и нужны ли реальные Legal/Terms/Privacy/Cookies URLs. Пока Page resource не обоснован.
9. **SEO/canonical/trailing slash.** Нужна фактическая выгрузка production URL/redirect/canonical (crawler/Search Console или одобренный crawl). В исходниках canonical slash непоследователен; не исправлять массово без неё.

### Дополнительные обнаруженные ошибки/неопределённости

- Cocktail Kingdom page имеет meta title `Birdy` и PDF Birdy; возможно ошибочная копия страницы.
- `/glassware/ishizuka/` имеет ссылки `#` вместо PDF, хотя похожая Ishizuka Barware page имеет свой каталог.
- Каталоговая страница содержит 92 PDF ссылки (46 карточек × 2 варианта), тогда как в asset tree 123 файла: прочие документы используются бренд-страницами, либо legacy/unused. Требуется точная инвентаризация hash → URL → назначение.
- Есть три явных PDF-дубликата между `assets/catalogs/` и библиотечным путём. Совпадение байтов не означает, что все старые URL можно удалить; нужна alias map.
- Названия, slugs и filenames отличаются: `gabriel-glas`, `coctail-kingdom`, `solidwater`, `rinamenardi`, `arita-japan`, `resobject`, `yoshinyma.htm`, `vetvi` в `lightning/`, Uccello/Hibino. Публичный slug нельзя нормализовать по имени.
- Layout `default_black` используется как минимум `/catalogues`; category/brand pages преимущественно `default`. Одна только разница layout-theme не требует отдельной модели.
- Header содержит навигационные URL без `/catalogues` в desktop меню и с ним в mobile; ссылки должны сходиться на один route. В футере legal links — `#`.
- Production runtime, доступность карт/формы, состояние старых прямых URL и актуальный SEO-компонент требуют отдельной проверки; исходники этого не доказывают.
