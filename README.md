# GRHS

Веб-приложение на Laravel 13 с административной панелью Filament 5. Для работы нужны PHP 8.3+ и Node.js с npm.

## Установка

```bash
composer run setup
```

Команда устанавливает PHP- и JS-зависимости, создаёт `.env` из `.env.example` (если его ещё нет), генерирует ключ приложения, выполняет миграции и собирает frontend.

Если настраиваете окружение вручную, сначала создайте `.env`, укажите в нём параметры базы данных, затем выполните `php artisan key:generate` и `php artisan migrate`.

## Локальная разработка

```bash
composer run dev
```

Запускает настроенный Laravel-процесс разработки. Для отдельного запуска frontend-сборщика с автоматическим обновлением браузера используйте:

```bash
npm run dev
```

Остановите dev-сервер перед запуском production-сборки.

## Frontend

```bash
npm run build
```

Собирает CSS и JavaScript для использования приложением. Запускайте после изменений frontend-кода перед проверкой production-сборки или развёртыванием.

## База данных

```bash
php artisan migrate
```

Применяет ожидающие миграции к базе данных из `.env`.

```bash
php artisan migrate:status
```

Показывает, какие миграции уже применены.

## Telegram-заявки

Укажите токен бота в `TELEGRAM_BOT_TOKEN` в `.env`, затем отправьте боту любое сообщение. Чтобы проверить полученные сообщения и узнать `chat_id`, откройте:

`https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`

В ответе найдите `message.chat.id` и укажите его в Filament → Telegram Settings. Для группы добавьте бота в группу и отправьте туда сообщение; используйте отрицательный `chat_id` из ответа. Не публикуйте URL с настоящим токеном.

## Кэш и обслуживание

```bash
php artisan optimize
```

Подготавливает конфигурацию, маршруты, события и Blade-шаблоны для production, сохраняя их в кэш. Запускайте при развёртывании после обновления кода и настроек.

```bash
php artisan optimize:clear
```

Удаляет кэши, созданные оптимизацией, и очищает кэш приложения. Полезно после изменения конфигурации или при диагностике устаревших данных.

Для точечной очистки можно использовать:

```bash
php artisan config:clear  # кэш конфигурации
php artisan route:clear   # кэш маршрутов
php artisan view:clear    # скомпилированные Blade-шаблоны
```

## Sitemap и планировщик

Создать или обновить статический `public/sitemap.xml` можно вручную:

```bash
php artisan sitemap:generate
```

Команда обновляет файл, только если его содержимое изменилось. Она зарегистрирована в Laravel Scheduler в `routes/console.php` и запускается каждый час с защитой от параллельного выполнения.

Чтобы Scheduler запускался автоматически на production, добавьте стандартную задачу cron от имени пользователя приложения. Замените `/path/to/project` на абсолютный путь к проекту:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

После каждого production-деплоя запустите `php artisan sitemap:generate` вручную, чтобы sitemap был актуален сразу, не дожидаясь следующего часового запуска.

## Тесты и стиль кода

```bash
composer run test
```

Очищает кэш конфигурации и запускает тесты проекта.

```bash
php artisan test --compact
```

Запускает тесты напрямую с компактным выводом. Чтобы запустить один файл, добавьте его путь, например `php artisan test --compact tests/Feature/ExampleTest.php`.

```bash
vendor/bin/pint --dirty --format agent
```

Форматирует изменённые PHP-файлы по правилам Laravel Pint.

## Полезные команды Artisan

```bash
php artisan list
```

Показывает доступные команды. Для справки по конкретной команде используйте `php artisan help <команда>`.

```bash
php artisan route:list
```

Выводит зарегистрированные маршруты. Для фильтрации доступны параметры `--path=admin`, `--method=GET` и `--name=<имя>`.

```bash
php artisan about
```

Показывает сводку о приложении и его окружении.
