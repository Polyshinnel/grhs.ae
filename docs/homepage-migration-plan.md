# План переноса главной страницы GRHS

## Область и достоверность наблюдений

План составлен по `old-theme/pages/home.htm`, подключённым layout/partials, `old-theme/assets/css/style.css` (и сверке с исходным SCSS), `old-theme/assets/js/main.js`, медиафайлам и аудиту `docs/legacy-site-audit.md`. Внешние API и шаблоны October CMS не переносятся буквально.

`https://grhs.ae/` доступен для текстового чтения: в открытой странице подтверждаются меню, текстовые секции, заголовки и состав футера. Инструмента браузерного рендеринга с контролируемыми viewport здесь нет, поэтому desktop/mobile визуальная проверка и сравнение пиксель-в-пиксель **не выполнены**. Изображения и видеоповедение по сайту в браузере не подтверждены; их расположение ниже выводится из исходников темы. Следующие шаги включают проверку в настоящем браузере.

В репозитории нет `.ai/rules/`; дополнительных правил, ограничивающих создание этого документа, не обнаружено. Текущая конфигурация уже указывает Tailwind CSS `^4.0.0` и `@tailwindcss/vite` `^4.0.0` в `package.json`.

## Главная сверху вниз

1. **Шапка поверх hero.** Desktop логотип GRHS и горизонтальные ссылки: Main, Tableware, Glassware, Barware, Kitchenware, Poolware, Cutlery, Wood, Uccello, Contacts. На ширине до 800 px горизонтальная навигация скрывается, появляется круглая кнопка меню. Раскрытая панель — белая карточка с изображением-декором и вертикальным списком, в котором дополнительно есть Catalogues. Активная ссылка определяется по `window.location.pathname` в jQuery; для корня логика слеша особая.
2. **Полноэкранный видео hero.** Текст «Quality hospitality / equipment & tableware», ссылка Catalogues. Видео на всю площадь с `object-fit: cover`, приглушённым автоматическим зацикленным воспроизведением и poster. В `<source>` для viewport до 767 px выбирается отдельный мобильный ролик, иначе — другой ролик.
3. **Бегущая лента партнёров.** Светлый фон `#FAF8F3`, 43 исходных логотипа (файлы 1–2 и 4–43; файла 3 среди них нет), затем такой же набор дублируется для бесшовного прохода. Анимация 60 s линейно, пауза при наведении; на мобильном — 80 s и крупнее логотипы.
4. **Bestsellers.** Заголовок и ссылка «all catalogues», четыре карточки: Kenai Ceramics, Uccelo, Le Coq Porcelain, Gien; у каждой фото, название и описание. Это не слайдер с кнопками: лента бесконечно зациклена клонированием карточек, перемещается перетаскиванием мышью/указателем и защёлкивается на ближайший шаг. На desktop ширина карточки 420 px, промежуток 24 px; на мобильном ширина viewport минус 48 px (не меньше 160 px). Контент карточки и картинки находятся прямо в шаблоне.
5. **About us.** Две текстово-визуальные строки. Первая: фото `about-1`, заголовок и два абзаца. Вторая: два абзаца и `about-2`, с обратным расположением. На мобильном каждая строка становится колонкой; вторая сохраняет смысловой порядок текста над фото через `column-reverse`.
6. **Categories.** Заголовок CATEGORIES, ссылка «all catalogues», сетка из 12 плиток с наложенной подписью: Tableware, Glassware, Bar glass, Bar tools, Cutlery, Steak knives, Wood, Kitchen accessories, Asian concepts, Buffet & hotel supplies, Metal & copperware, Poolware. Desktop — 6 столбцов, мобильный — 2; каждая плитка квадратная. Ссылки идут на существующие разделы категории, отдельные концепты перенаправлены на ближайший существующий раздел (например Bar tools → `/barware/`).
7. **WhatsApp CTA.** Блок с текстами «Make an appointment.» и «Send us a message and we’ll get back to you shortly.» и кнопкой перехода в WhatsApp `wa.me/971585338524`. Desktop высота 420 px; на мобильном высота по содержимому.
8. **Our brands.** Заголовок OUR BRANDS, ссылка «view all brands» с `href="#"` (заглушка), лента из восьми логотипов и дублированного набора. Desktop — примерно восемь видимых логотипов, скорость 60 s; мобильная карточка шириной 40vw, 80 s. Пауза при наведении и отключение CSS-анимации при `prefers-reduced-motion: reduce`.
9. **Footer из layout.** Контакты: адрес в Business Bay, телефон, email, Instagram; логотип; Bitrix24 loader для формы; Copyright 2026 и четыре юридические ссылки с `#` (заглушки). Важно: ссылка-иконка телефона имеет `tel:+971569421220`, а показанный номер и основной CTA — `+971 58 533 8524`; сверить, какой номер верный.
10. **Фиксированные плавающие действия из footer partial.** Две круглые кнопки внизу справа: WhatsApp и Contact. Начальное состояние показывает обе кнопки-переключателя, но WhatsApp-ссылка/контактная мини-форма скрыты. Нажатия меняют иконку на крест и раскрывают соответствующий блок. Поля мини-формы только размечены в HTML; в `main.js` отправки формы нет. Отдельно в Bitrix24 загружается CRM форма по клику.

## Общие компоненты, layout и структура Laravel

Простая структура без компонентов на каждую мелкую деталь:

```text
resources/views/
  layouts/app.blade.php                 # head, Vite, body, header, @yield, footer, floating actions
  components/site/header.blade.php      # logo, desktop nav, mobile menu trigger + panel
  components/site/footer.blade.php      # contact data, CRM mount/script, legal links
  components/site/floating-contact.blade.php
  components/home/section-heading.blade.php  # только если одинаковый заголовок/CTA используется 2+ раза
  pages/home.blade.php                  # композиция и небольшие статические секции
```

`pages/home.blade.php` использует `layouts.app`. Секции, не нуждающиеся в повторном использовании или редакторском управлении, оставить в том же view; не превращать изображения, текстовые абзацы или отдельную карточку в Blade-компоненты без второго реального потребителя. Одна переиспользуемая секция заголовка может принимать heading и ссылку, если эта повторяемость сохранится после визуальной сверки. Не вводить модели, миграции или Filament ресурсы для этой статической реализации.

Layout отвечает за `lang`, meta/SEO, viewport, Vite CSS/JS, header и footer. Нынешняя home-страница объявлена `canonical_url=https://grhs.ae/`; meta-title и meta-description лежат во front matter. Перенести эти конкретные значения/контракт в существующий механизм метаданных проекта либо задать страницу, не теряя canonical и описания. GTM ID `GTM-WMRRM4GQ` включать через существующую конфигурацию только после подтверждения его актуальности.

Общие повторяющиеся элементы: абсолютная шапка поверх контента; адаптивный контейнер и широкие full-bleed секции; Lato как базовый текстовый шрифт; Haigrast Serif определён локально, но в разметке главной не применяется; контрастные uppercase заголовки; серо-бежевые/оливковые акценты; футер контактов и плавающие быстрые действия.

## Установленные визуальные параметры и адаптивность

Значения взяты из CSS home/layout, а не подобраны визуально:

| Элемент | Исходное значение |
|---|---|
| Основной шрифт | Lato, Google Fonts: 300, 400, 700, 900 и italic 300/400; fallback sans-serif |
| Локальный дополнительный шрифт | `old-theme/assets/fonts/Haigrast-Serif.ttf`, `font-display: swap`; правила есть, но главный home-контент явно его не использует |
| Hero | 100vh; до 1400 px заменяется на 700 px; mobile до 800 px — 100vh; `object-fit: cover`, позиция center |
| Header | абсолютный, `top:0`, z-index 300, высота 90 px, horizontal padding 30 px; логотип шириной 50 px |
| Общий `.new-common-section` | width 100%, сверху 117 px, слева/справа 60 px; до 800 px: 72/24 px; до 480 px: 56/16 px |
| Section heading | 30 px / 600; ссылка 21 px / 600; до 800 px заголовок 24 px и ссылка 18 px; до 480 px заголовок 22 px и ссылка 17 px |
| Bestsellers | фото 420×572 px, шаг с gap 24 px; горизонтальные поля секции 60 px (mobile 20 px, до 480 — 16 px) |
| About | gap между строками 100 px; между фото и текстом 150 px (до 1200 — 80 px); основной текст 21 px, line-height 1.45; mobile stack до 800 px |
| Categories | 6 колонок desktop, 2 до 800 px, квадратные карточки; подписи 30 px desktop, 20 px mobile, 17 px до 480 px |
| WhatsApp CTA | `#e9e6de`, текст `#1d1d1b`, 420 px desktop; mobile padding 48/24/52 px и до 480 px 40/16/44 px |
| Ленты | partner background `#FAF8F3`; высоты 105 px desktop / 80 px mobile; логотипы 90 px / 60 px |
| Footer container | 1180 px; до 1200 px 850 px; mobile 80% ширины. Footer padding 70 px сверху/снизу, на mobile 40 px |
| Плавающие кнопки | 60×60 px, справа 15 px, снизу 50 px, шаг сверху 10 px; фиксированный слой z-index 300 |

Брейкпоинты в CSS неоднородны: основные home-адаптации `max-width: 800px`, узкие телефоны `480px`, некоторые блоки `1200px`; шапка также имеет 1400/1200 px. В carousel JS используется `matchMedia('(max-width: 800px)')`, а `<source>` мобильного видео — строго `max-width: 767px`. При реализации на Tailwind 4 не полагаться на дефолтные `md/lg` как точные аналоги: определить один набор явно именованных theme breakpoints/custom media с фактическими точками 480/800/1200/1400 и отдельно сохранить видео-порог 767 px, либо использовать произвольные media variants там, где это проще.

Основные цвета, подтверждённые CSS: `#1D1D1B` (тёмный текст), `#FAF8F3` (светлый фон ленты), `#e9e6de` (CTA), `#979A85`/`#9EA08A` (оливковые интерактивные элементы), `#A2664F` (в другой части темы, не подтверждён на главной), белый/чёрный. Определить окончательную палитру по screenshot после получения browser render.

## Точные медиа главной в `old-theme/`

Ниже перечислены пути, реально указанные в `pages/home.htm` и общих partials. Их копирование/перемещение в Laravel на этом этапе не выполняется.

| Назначение | Пути относительно `old-theme/` |
|---|---|
| Header logo, mobile menu icon/decoration | `assets/images/logo-header.svg`, `assets/images/menu.svg`, `assets/images/menu-img.svg` |
| Hero video + poster | `assets/images/main-video-mob.mp4`, `assets/images/main-video-mod.mp4`, `assets/images/main/main-poster.jpg` |
| Hero fallback/возможный старый ролик | `assets/images/main/main-video.mp4` (файл присутствует, но home-markup на него не ссылается) |
| Partner marquee | `assets/images/about/partners/{1,2,4-43}.png`; список продублирован в разметке. `3.png` существует, но не используется на homepage |
| Bestsellers photos | `assets/images/main/slider/{1,2,3,4}.jpg` |
| About photos | `assets/images/main/about-1.jpg`, `assets/images/main/about-2.jpg` |
| Category tiles | `assets/images/main/categories/{1-12}.jpg` |
| Link arrows | `assets/images/main/arrow.svg` |
| Our brands marquee | `assets/images/main/brands/{1-8}.png`; набор повторён для цикла |
| WhatsApp CTA icon | `assets/images/main/whatsapp.svg` |
| Footer logo/icons | `assets/images/logo.svg`, `assets/images/footer/{geo,phone,mail,insta,whatsapp,chat1,chat2,cross}.svg` |

Видео файлы по аудиту: `main-video-mob.mp4` около 366 KB, `main-video-mod.mp4` около 1.23 MB, `main/main-video.mp4` около 41.7 MiB. Использовать два файла из `<source>` как фактические версии, `main-video.mp4` не считать необходимым для переноса, пока не выяснится причина его существования. В HTML постер задан всегда; проверить, достаточно ли его для reduced motion, запрета autoplay и экономии трафика. Скопировать только требуемые исходники, проверив размеры, кодеки и responsive crop до реализации.

Важное несоответствие с аудитом и snapshot: все ссылки на partner assets в homepage идут в `assets/images/about/partners/`, а не в `assets/images/partners/`. Оба каталога существуют, но слайд использует первый. На сайте убедиться, что production отдаёт именно эти пути.

## JavaScript, October и внешние зависимости

- `old-theme/layouts/default.htm` подключает jQuery 3.7.1 с Google CDN, `assets/js/main.js`, `{% framework extras %}`, `{% scripts %}` и `{% component 'SeoCmsPage' %}`. Это October CMS API, не часть нового Laravel layout.
- `main.js` использует jQuery для активной desktop-ссылки, переключения mobile menu через `slideToggle`, переключения иконок плавающих CTA и показа/скрытия WhatsApp/контактной формы через `fadeIn/fadeOut`.
- Большой inline script в `home.htm` реализует бесконечную carousel ленту bestsellers. Использует Pointer Events, pointer capture, измерения viewport, debounce resize на 100 ms, клонирует исходные четыре карточки до и после набора и снапит позицию после drag. Это единственная сложная домашняя интерактивность; переносить как небольшой модуль vanilla JS, без добавления jQuery ради неё.
- CSS ленты partners/brands уже анимирует marquee; hover pause и `prefers-reduced-motion` предусмотрены. Их поведение не должно зависеть от JS.
- Hero relies on native `<video autoplay muted loop playsinline>` and media-qualified `<source>`; мобильные браузеры и их autoplay policy нужно проверить.
- Bitrix24 loader: `https://cdn-ru.bitrix24.ru/b24247626/crm/form/loader_10.js`, data attr `click/10/2lqj1v`. GTM: `GTM-WMRRM4GQ`. Google Fonts Lato — CSS `@import`. WhatsApp, Instagram, `tel:`, `mailto:` — внешние destinations.
- Форма в плавающем popup сама по себе не отправляет данные. Не воспроизводить видимость работающей формы без выяснения: использовать ли Bitrix24, backend endpoint либо удалить popup в пользу реального CRM trigger. На скриншоте/проверке проверить появление Bitrix формы после клика.
- В теме `assets/endpoints/` есть PHPMailer vendor, но homepage markup не доказывает, что он участвует в отправке этой формы. Не считать его Laravel интеграцией и не переносить вслепую.
- `|theme`, `{% page %}`, `{% partial %}`, `{% styles %}`, `{% scripts %}`, `{% framework extras %}` и CMS layout/frontmatter заменить обычным Blade/Vite и механизмом проекта; SEO-компонент October `SeoCmsPage` не переносить без выяснения фактического output.

## Что нужно выяснить браузером

1. Снять screenshots минимум на desktop (например 1440×900 и широкий 1920 px) и mobile (390×844; дополнительно планшет), включая страницу после полной загрузки и скролл до footer. Сравнить межсекционные отступы, crops/позиции видео и фото, высоту/фиксированность шапки, размеры текста и фактический вид секций.
2. Установить desktop hero video и кадрирование; проверить autoplay, poster до загрузки, поведение при reduced motion и мобильный ролик. Выяснить, намеренно ли основной `main-video.mp4` оставлен неиспользованным.
3. Проверить цикл двух marquee: непрерывность без скачка, скорость, видимое число логотипов на разных ширинах, паузу при hover и reduced motion. Установить, должен ли первый список содержать логотип `3.png`.
4. Проверить carousel при drag мышью и touch, при resize/orientation change, коротком и быстром свайпе; понять количество одновременно видимых карточек и нужно ли сохранять нативное горизонтальное скроллирование для клавиатуры.
5. Проверить мобильное меню: закрывается ли при выборе пункта, по Escape и клику вне панели, блокирует ли прокрутку; исходный JS эти сценарии не описывает.
6. Уточнить роль двух плавающих кнопок и CRM: как показывается форма Bitrix, работает ли мини-форма, куда отправляет заявку; проверить фактические телефонные номера.
7. Проверить заглушки «view all brands» и legal links, корректность `Uccello`/`Uccelo`/`Uccello` в меню, карточках и данных, а также прямой переход со всех ссылок категории.
8. Убедиться, какие изображения/видео реально загружаются production. Источник содержит ссылки на `assets/images/about/partners/`; факт существования локальных файлов не доказывает отсутствие production 404.
9. Сравнить используемую шапку и footer live страницы с файлом `default.htm`: внешний web crawl подтверждает ссылочную структуру, но не геометрию, а состояние источника и runtime может отличаться.

## Небольшие проверяемые этапы переноса

1. **Зафиксировать baseline.** Снять и сохранить desktop/mobile screenshots и короткое видео поведения меню, двух лент, carousel, плавающих действий и hero. Проверить asset HTTP статусы; зафиксировать вопросы выше, которые влияют на интерфейс.
2. **Составить manifest медиа главной.** Сверить файлы из таблицы с фактической разметкой и live Network, согласовать корректные номера телефонов и master assets; затем определить новые публичные пути Laravel. Пока оставить каталоги и другие страницы вне области.
3. **Собрать общую оболочку страницы.** Добавить Blade layout с SEO slots/метаданными, Vite/Tailwind, Header (desktop/mobile), footer и floating actions. На этом этапе использовать заглушку `@yield('content')`, проверить геометрию на двух viewport.
4. **Собрать статическую верхнюю часть.** Hero с poster/video, ссылка каталога и partner marquee. Проверить размеры, overlay/контраст, автоматическое воспроизведение, fallback и reduced motion.
5. **Перенести контентные секции.** Bestsellers, About, Categories, WhatsApp CTA в исходном порядке. Сначала верстка без усложнения данных; сверить тексты и ссылки с `home.htm`, проверить desktop/mobile screenshots.
6. **Добавить интерактивность целевыми модулями.** Bestsellers drag/infinite loop; menu toggle/accessibility; floating actions; CSS marquee. Использовать vanilla JS, state через классы/ARIA, не зависеть от jQuery/October. Для каждой — вручную проверить keyboard, touch, resizing и reduced motion.
7. **Включать внешние интеграции после проверки.** GTM/Bitrix24 только с подтверждением актуальных IDs и поведения; проверить отсутствие дублирования скриптов и блокирующей загрузки. Указать реальную форму/обработчик вместо пустой разметки формы.
8. **Финальное визуальное сравнение и регрессия главной.** Одни и те же размеры окна, изображения, прокрутка и interaction states для старого и нового сайтов; проверить консоль/network, ссылки, alt/ARIA, video fallback, reduced motion и доступность навигации. Остальные страницы, модели и админка в рамках этого этапа не затрагиваются.

## Источники в репозитории

- `docs/legacy-site-audit.md` — общий аудит October CMS и открытых вопросов.
- `old-theme/pages/home.htm` — секции главной, точные тексты, ссылки и локальные inline carousel.
- `old-theme/layouts/default.htm` — head/SEO/GTM, stylesheet и script loading.
- `old-theme/partials/site/header.htm` — desktop/mobile nav.
- `old-theme/partials/site/footer.htm` — контакты, CRM loader, футер и floating actions.
- `old-theme/assets/css/style.css`, `old-theme/assets/css/style.scss` — фактические стилевые параметры и breakpoints.
- `old-theme/assets/js/main.js` — общие menu, active-link и floating-action interactions.
- `package.json` — текущие Vite/Tailwind версии проекта.
