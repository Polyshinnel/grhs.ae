# Технический аудит темы старого сайта GRHS (October CMS)

Аудит выполнен по исходникам `old-theme/` в режиме чтения. Выводы описывают только тему: runtime October CMS, базу данных, плагины и состояние production не проверяли. В `old-theme/` найдена 47 файлов страниц; один URL указан в двух файлах, поэтому это 47 деклараций маршрутов и 46 уникальных значений `url`.

## 1. Карта сайта и маршруты

Страницы — обычные CMS-файлы, содержимое которых в основном захардкожено в Twig/HTML. Категории и брендовые страницы не используют в теме динамические параметры маршрута или запросы к БД. `/catalogues` содержит каталог как статическую разметку, а поиск/фильтры, если они работают, должны быть реализованы клиентским JS или внешним кодом: в просмотренном `main.js` их логики нет. Убедиться в полном поведении без окружения October нельзя.

```text
/
├── /contacts
├── /catalogues
├── /barware
│   ├── /barware/aoyama
│   ├── /barware/birdy
│   ├── /barware/coctail-kingdom
│   ├── /barware/gabriel-glas
│   └── /barware/ishizuka
├── /cutlery
│   ├── /cutlery/eme
│   └── /cutlery/sabre
├── /glassware
│   ├── /glassware/aoyama
│   ├── /glassware/glassmade
│   ├── /glassware/hirota-glass  [конфликт: два файла]
│   ├── /glassware/ishizuka/    [слеш в значении url]
│   ├── /glassware/sklarna
│   ├── /glassware/solidwater
│   └── /glassware/yoshinuma
├── /kitchenware
│   ├── /kitchenware/casalinghi
│   └── /kitchenware/ilsa
├── /lighting
│   └── /lighting/vetvi
├── /outdoor
│   └── /outdoor/kavehome
├── /poolware
│   └── /poolware/glassforever
├── /tableware
│   ├── /tableware/arita-japan
│   ├── /tableware/bitossi-home
│   ├── /tableware/gien
│   ├── /tableware/hibino
│   ├── /tableware/kenai
│   ├── /tableware/kurieto
│   ├── /tableware/le-coq
│   ├── /tableware/miyama
│   ├── /tableware/molleni
│   ├── /tableware/resobject
│   ├── /tableware/rinamenardi
│   └── /tableware/soul
├── /ucello
└── /wood
    ├── /wood/trud
    └── /wood/woodeez
```

### Полный список деклараций

Все перечисленные ниже маршруты — статические страницы темы. Последний столбец указывает файл-источник; суффикс `.htm` опущен.

| URL | Источник |
|---|---|
| `/` | `pages/home` |
| `/contacts` | `pages/contacts` |
| `/catalogues` | `pages/catalogue` |
| `/barware` | `pages/barware` |
| `/barware/aoyama` | `pages/barware/aoyama` |
| `/barware/birdy` | `pages/barware/birdy` |
| `/barware/coctail-kingdom` | `pages/barware/coctail_kingdom` |
| `/barware/gabriel-glas` | `pages/barware/gabriel` |
| `/barware/ishizuka` | `pages/barware/ishizuka` |
| `/cutlery` | `pages/cutlery` |
| `/cutlery/eme` | `pages/cutlery/eme` |
| `/cutlery/sabre` | `pages/cutlery/sabre` |
| `/glassware` | `pages/glassware` |
| `/glassware/aoyama` | `pages/glassware/aoyama` |
| `/glassware/glassmade` | `pages/glassware/glassmade` |
| `/glassware/hirota-glass` | `pages/glassware/hirota-glass` **и** `pages/hirota-glass` |
| `/glassware/ishizuka/` | `pages/glassware/ishizuka` |
| `/glassware/sklarna` | `pages/glassware/sklarna` |
| `/glassware/solidwater` | `pages/glassware/solid` |
| `/glassware/yoshinuma` | `pages/glassware/yoshinyma` |
| `/kitchenware` | `pages/kitchenware` |
| `/kitchenware/casalinghi` | `pages/kitchenware/casalinghi` |
| `/kitchenware/ilsa` | `pages/kitchenware/ilsa` |
| `/lighting` | `pages/lighting` |
| `/lighting/vetvi` | `pages/lightning/vetvi` (путь каталога `lightning` не совпадает с URL `lighting`) |
| `/outdoor` | `pages/outdoor` |
| `/outdoor/kavehome` | `pages/outdoor/kavehome` |
| `/poolware` | `pages/poolware` |
| `/poolware/glassforever` | `pages/poolware/glassforever` |
| `/tableware` | `pages/tableware` |
| `/tableware/arita-japan` | `pages/tableware/arita` |
| `/tableware/bitossi-home` | `pages/tableware/bitossi-home` |
| `/tableware/gien` | `pages/tableware/gien` |
| `/tableware/hibino` | `pages/tableware/hibino` |
| `/tableware/kenai` | `pages/tableware/kenai` |
| `/tableware/kurieto` | `pages/tableware/kurieto` |
| `/tableware/le-coq` | `pages/tableware/le-coq` |
| `/tableware/miyama` | `pages/tableware/miyama` |
| `/tableware/molleni` | `pages/tableware/molleni` |
| `/tableware/resobject` | `pages/tableware/resobject` |
| `/tableware/rinamenardi` | `pages/tableware/rhina` |
| `/tableware/soul` | `pages/tableware/soul-studio` |
| `/ucello` | `pages/ucello` |
| `/wood` | `pages/wood` |
| `/wood/trud` | `pages/wood/trud` |
| `/wood/woodeez` | `pages/wood/woodeez` |

`pages/contacts.htm.save` — резервная копия, не отдельная декларация маршрута.

### Общие элементы интерфейса

- `layouts/default.htm`, `layouts/default_black.htm`: HTML-head, SEO-компонент, GTM, стили, шапка, содержимое, подвал, jQuery и October framework scripts. Первый layout использует `header`, второй — `header-black`.
- `partials/site/header.htm`, `header-black.htm`: навигация и мобильное меню. Список в десктопном и мобильном меню различается: ссылка на `/catalogues` есть только в мобильном меню; также отличаются подписи Uccello.
- `partials/site/footer.htm`: контакты, Instagram, внешний Bitrix24 form loader, WhatsApp, всплывающая контактная форма и ссылки-заглушки на legal pages.
- Общие визуальные шаблоны в страницах: hero/banner, плиточная сетка категорий/брендов, информационные секции бренда с изображениями, ссылки на каталоги.
- `partials/explain/ajax.htm` и `plugins.htm` выглядят как демонстрационные/справочные файлы темы; подтверждения подключения к рабочим страницам нет.

## 2. Контент и предполагаемые сущности

### Что захардкожено

- Категории в навигации и плитках: tableware, glassware, barware, kitchenware, poolware, cutlery, wood, lighting, outdoor; отдельная `/ucello`.
- Производители/бренды, их slug, описание, изображения, логотипы, заголовки и каталоги находятся в отдельных файлах страниц и HTML-блоках.
- `/catalogues` содержит большой повторяющийся список карточек PDF: название бренда, категория (`data-category`), бренд (`data-brand`), концепты (`data-concept`), признак `data-large`, изображения и два варианта PDF-ссылки (скачивание и просмотр).
- На брендовых страницах повторяется композиция из логотипа, cover, текста/галереи, ссылки на PDF. Данные о товарах/SKU, вариантах, ценах и остатках в изученной теме не обнаружены.
- Некоторые ссылки/ярлыки отличаются между меню, карточками и маршрутом: например, `gabriel-glas` в URL при отображаемом Gabriel Glass, `coctail-kingdom`, `solidwater`, `rinamenardi`.

### Рекомендуемая модель данных для управления через Filament

Предложение основано на видимых повторяющихся данных, не на подтверждённом бизнес-процессе:

1. **Category**: название, slug, описание, порядок, hero/изображение, статус публикации; иерархия через nullable `parent_id`, если нужны подкатегории.
2. **Brand**: имя/slug, логотип, cover, краткое и полное описание, SEO-поля, активность/порядок.
3. **BrandCategory** (pivot): многие-ко-многим, поскольку один бренд может находиться в разных категориях (например Aoyama в barware и glassware, Ishizuka в barware и glassware).
4. **Catalog**: связь с брендом и категорией, отображаемое имя, файл оригинала, файл просмотра/оптимизированная версия, порядок, статус/дата актуальности.
5. **Concept** и pivot `catalog_concept`: концепты в фильтре каталога (`Italian`, `Japanese`, `Bar`, `Beach club` и т. п.) связаны с каталогами через список ID.
6. **BrandPageSection** или гибкие блоки содержимого бренда: тип блока, текст/изображения, позиция; применять, если редакторам нужно управлять страницами без ручного редактирования Blade.
7. **Page/SEO metadata**: либо поля SEO у каждой сущности, либо отдельная управляемая CMS-страница для статических разделов. Выбор зависит от того, какие разделы должны редактироваться в админке.

Связи: Category ↔ Brand — многие-ко-многим; Brand → Catalog — один-ко-многим; Catalog ↔ Concept — многие-ко-многим; Brand → BrandPageSection — один-ко-многим. Реальные товары следует моделировать отдельно только если предоставлены их данные и требования к каталогу.

## 3. Внешние зависимости и данные вне темы

- October CMS Twig/theme helpers (`|theme`), CMS page/layout механизм, `{% page %}`, `{% partial %}`, `{% styles %}`, `{% scripts %}`, `{% framework extras %}`.
- Компонент `SeoCmsPage` — внешний компонент, вероятно plugin October SEO. В теме нет manifest/версии плагина; точное имя пакета и конфигурация требуют проверки исходной установки October.
- Google Tag Manager `GTM-WMRRM4GQ` в обоих layouts.
- jQuery 3.7.1 через Google CDN.
- Bitrix24 CRM form loader `loader_10.js` в footer.
- Mapbox GL JS/CSS на странице контактов; ключ/настройки и связанный скрипт зависят от значений в исходнике страницы/внешнего сервиса.
- WhatsApp `wa.me`, Instagram, телефон и email — внешние каналы связи.
- `assets/endpoints/` включает `composer.json` с PHPMailer `^6.8`, lock-файл и встроенный vendor. По теме нельзя установить, доступен ли endpoint в production, его серверный обработчик, SMTP credentials или куда отправляется форма. Эти данные нельзя считать переносимыми из темы.
- Не представлены: база October, CMS plugin inventory/config, содержимое backend-managed settings, реальные записи/пользователи, sitemap/robots/redirect rules, окружение и аналитика, параметры почтового сервера, рабочие credentials Mapbox/Bitrix.

## 4. Медиа и прочие файлы

Подсчёт: все обычные файлы внутри `old-theme/`, без `.git/` и встроенного `vendor/`; размеры в байтах, суммирование по расширению. Итого 620 файлов и около 2,59 ГБ (2,52 GiB). PDF занимают 2,51 ГБ (2,34 GiB) из этого объёма.

| Тип | Количество | Общий размер |
|---|---:|---:|
| PDF | 123 | 2,509,578,887 B (~2.34 GiB) |
| JPG/JPEG | 209 | 47,137,187 B (~44.9 MiB) |
| MP4 | 3 | 45,409,432 B (~43.3 MiB) |
| PNG | 129 | 11,868,511 B (~11.3 MiB) |
| WebP | 42 | 4,715,774 B (~4.50 MiB) |
| SVG | 48 | 554,438 B (~541 KiB) |
| TTF | 1 | 201,440 B (~197 KiB) |
| CSS, SCSS, map, JS | 4 | ~69 KiB суммарно |
| HTML, HTM, YAML, JSON, lock, ICO, SAVE и без расширения | 61 | ~300 KiB суммарно |

Основные тяжёлые файлы:

| Файл | Размер |
|---|---:|
| `assets/catalogs/new-catalogs/LE-COQ.pdf` | 342.8 MB |
| `assets/catalogs/new-catalogs/Gien.pdf` | 307.7 MB |
| `assets/images/catalog-block/catalogs/asian-concepts/youbi.pdf` | 208.3 MB |
| `assets/images/catalog-block/catalogs/tableware/my-ceramics.pdf` | 167.0 MB |
| `assets/images/catalog-block/catalogs/tableware/GIEN.pdf` | 88.9 MB |
| `assets/images/main/main-video.mp4` | 43.8 MB |
| `assets/images/catalog-block/catalogs/tableware/pura-sangre.pdf` | 71.8 MB |
| `assets/images/catalog-block/catalogs/tableware/le-coq.pdf` | 42.6 MB |

Для просмотра дополнительно встречаются файлы `catalogs-compressed`, но некоторые весят десятки мегабайт; слово compressed не гарантирует приемлемый web-размер. Следует проверить PDF оптимизацию/страничное превью и видеокодеки, разрешение и варианты постеров перед переносом. Остальные MP4: `main-video-mod.mp4` и `main-video-mob.mp4`; названия предполагают отдельные варианты, но их фактическое использование надо сверить с разметкой и поведением сайта.

По SHA-256 найдено 23 группы полностью идентичных файлов (всего 24 лишних копии в этих группах): повторяются изображения/логотипы и три PDF (`hirota.pdf`, `kurieto.pdf`, `molleni.pdf`) в `assets/catalogs/` и `assets/images/catalog-block/catalogs/`. Это технические дубликаты по байтам; удалять или объединять их без проверки URL-совместимости нельзя.

Неиспользуемые ресурсы по одной теме достоверно установить нельзя. Сверка файлов с буквальными путями неполна из-за генерации URL фильтром `|theme`, CSS background URLs, JS, потенциальных внешних ссылок и отсутствующих каталогов в текущем снимке. В исходниках есть `assets/catalogs/` и отдельный каталог `assets/images/catalog-block/catalogs/`; множество ссылок каталожной страницы указывает непосредственно на второй путь. Наличие части неиспользуемых/дублирующих материалов вероятно, но это не доказательство неиспользования в production.

## 5. SEO и сохранение адресов

- Все 47 page front matter имеют `meta_title`, `robot_index = index`, `robot_follow = follow`; только 32 имеют `meta_description`, 12 — `canonical_url`.
- В layouts выводятся title/description/title meta и `{% component 'SeoCmsPage' %}`. Без установленного компонента нельзя установить, какие именно дополнительные meta-теги он генерирует.
- Явно присутствует Open Graph image через переменную `og_img`, устанавливаемую в `onStart()` на части страниц. Наличие `og:title`, `og:description`, `og:url`, `twitter:*` в исходниках темы не подтверждается. Значения Open Graph image не единообразны; большинство ссылается на изображения по абсолютному адресу `https://grhs.ae/themes/goldenratio/...`.
- Canonical-адреса иногда имеют завершающий slash, иногда нет. Два файла (`pages/glassware/hirota-glass.htm` и `pages/hirota-glass.htm`) объявляют один URL `/glassware/hirota-glass`; front matter у них одинаковый по title, description и canonical, но файлы не побайтово идентичны. Какая страница фактически побеждает при разрешении маршрута и различается ли значимый контент, без October runtime установить нельзя.
- Обнаружены расхождения внутренних ссылок и page URL: например, категория barware ссылается на `/barware/gabriel-glas` и `/barware/coctail-kingdom`; часть ссылок на брендовые страницы использует разные slug/орфографию. Перед миграцией нужен crawl старого сайта или экспорт списка URL из Search Console/аналитики и проверка HTTP-статусов.
- Важно сохранить все 46 уникальных URL (включая точные slash-варианты после установления canonical поведения), URL PDF, а для изменённых адресов подготовить 301 redirect map. Особое внимание: `/glassware/hirota-glass`, `/glassware/ishizuka/`, `/barware/gabriel-glas`, `/barware/coctail-kingdom`, `/tableware/arita-japan`, `/tableware/rinamenardi`, `/tableware/soul` и прямые URL каталогов под `/themes/goldenratio/assets/...`.
- Динамические URL из темы не выявлены: маршруты страниц статические. Возможные URL из October CMS database/plugin или скрытых страниц по теме не восстановить. Идентификаторы концептов в `data-concept` и возможное серверное поведение фильтров также не дают оснований утверждать наличие динамических URL.

## 6. Рекомендации для нового Laravel-приложения

1. Сначала зафиксировать фактические маршруты старого production-сайта, canonical-правила, редиректы и список индексируемых URL; разрешить конфликт Hirota/Ishizuka и выбрать канонические slug.
2. Категории и брендовые посадочные страницы реализовать как управляемые сущности с уникальными slug и явной связью Brand–Category. Для ключевых SEO-страниц предусмотреть редактируемые title, description, canonical и Open Graph поля.
3. Каталоги хранить как отдельные записи с файлами оригинала и web-версии/просмотра, тегами концептов, категорией, брендом и порядком. Не встраивать каталоги и названия непосредственно в шаблоны.
4. Повторяемые визуальные блоки бренда сделать редактируемыми секциями с ограниченным набором типов. Статические юридические страницы и контактные данные оформить как явные CMS-страницы/настройки по требованиям редакторов.
5. В Filament дать управление категориями, брендами, связями, каталогами, концептами, медиаданными, SEO и публикацией. Для файлов использовать Laravel storage/media workflow, согласовав домен доставки, CDN, ограничения размера и версии URL.
6. Перенести GTM, карту, CRM-форму, email/SMTP и контактные реквизиты в конфигурацию/настройки нового приложения, сохраняя интеграции только после проверки владельцев и credentials. Не переносить секреты в код или отчёт.
7. План переноса медиа должен учитывать более 2.5 GB PDF, URL файлов и клиентский опыт. При оптимизации сохранять доступность архивных оригиналов и сопоставление старых адресов.

## 7. Открытые вопросы, на которые тема не отвечает

- Какой из двух вариантов страницы `/glassware/hirota-glass` является правильным и какой контент/indexing должен сохраниться?
- Какие страницы реально доступны и индексируются в production, есть ли скрытые страницы, redirects, sitemap и правила robots?
- Работают ли поиск/фильтры на `/catalogues`; где их исходная логика и каковы точные правила сопоставления категорий/концептов/брендов?
- Какие из 123 PDF актуальны, какие являются оригиналами/сжатыми копиями, какие должны остаться публично доступными? Почему есть сотни мегабайт у отдельных PDF?
- Какие каталоги перечислены как данные, отсутствующие в теме или имеющие ссылки на отсутствующие в snapshot файлы?
- Как устроена отправка контактных форм, куда идут заявки, и какие SMTP/Bitrix24 настройки используются?
- Какие версии October CMS, PHP и плагинов (включая `SeoCmsPage`) установлены и есть ли backend CMS записи, меняющие содержимое темы?
- Какой объём и структура каталога реальных товаров требуются в новом сайте: SKU, спецификации, цены, варианты, наличие или только маркетинговые страницы и PDF?
- Кто будет редактировать страницы/контакты/юридические документы, какие языки сайта и бизнес-правила публикации нужны?
- Какие URL файлов и страницы имеют внешние обратные ссылки/трафик и требуют обязательного сохранения?
