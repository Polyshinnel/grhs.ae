<?php

use App\Filament\Pages\TelegramSettings as TelegramSettingsPage;
use App\Models\TelegramSettings;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function prepareTelegramSettings(array $attributes = []): TelegramSettings
{
    return TelegramSettings::query()->forceCreate(array_merge([
        'id' => 1,
        'chat_id' => '-1001234567890123456',
        'use_proxy' => false,
        'http_proxy' => null,
    ], $attributes));
}

function fakeTelegramApi(array|callable $response = ['ok' => true]): void
{
    Http::preventStrayRequests();
    Http::fake([
        'https://api.telegram.org/*/sendMessage' => is_callable($response)
            ? $response
            : Http::response($response, 200),
    ]);
}

function contactInquiryPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'phone' => '+971 50 123 4567',
        'message' => 'Hello, I would like to request your catalogues.',
        'website' => '',
    ], $overrides);
}

it('sends the contacts enquiry with the configured string chat id and formatted content', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => '123456:secret-token']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload([
        'chat_id' => 'attacker-chat-id',
        'page_url' => 'https://attacker.example/phishing',
    ]))
        ->assertOk()
        ->assertExactJson(['message' => 'Thank you! Your message has been sent successfully.']);

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.telegram.org/bot123456:secret-token/sendMessage'
        && $request->data()['chat_id'] === '-1001234567890123456');

    expect(Http::recorded()->first()[0]->data()['text'])->toBe(
        "📩 New enquiry — GRHS\n\nForm: Contact Us\nName: John Smith\nEmail: john@example.com\nPhone: +971 50 123 4567\nMessage: Hello, I would like to request your catalogues.\n\nPage: ".rtrim((string) config('app.url'), '/').'/contacts',
    );
    expect(Http::recorded()->first()[0]->data())->not->toHaveKey('parse_mode');
});

it('sends quick contact fields without an empty message field', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    $this->postJson(route('contacts.quick'), contactInquiryPayload(['message' => null]))
        ->assertOk();

    Http::assertSent(fn ($request): bool => $request->data()['chat_id'] === '-1001234567890123456'
        && str_contains($request->data()['text'], 'Form: Quick Contact')
        && ! str_contains($request->data()['text'], 'Message:')
        && str_contains($request->data()['text'], 'Page: '.rtrim((string) config('app.url'), '/')));
});

it('reports delivery failure without leaking secrets or personal data when the token is missing', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => null]);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())
        ->assertStatus(503)
        ->assertExactJson(['message' => "We couldn't send your message right now. Please try again or contact us directly."])
        ->assertDontSee('token')
        ->assertDontSee('john@example.com');

    Http::assertNothingSent();
});

it('returns a delivery failure without calling Telegram when chat id is absent', function () {
    prepareTelegramSettings(['chat_id' => null]);
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertStatus(503);

    Http::assertNothingSent();
});

it('returns a delivery failure when Telegram rejects the message with an HTTP error', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    Http::preventStrayRequests();
    Http::fake(['https://api.telegram.org/*/sendMessage' => Http::response(['ok' => false], 400)]);

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertStatus(503);
});

it('returns a delivery failure when Telegram responds with ok false or malformed JSON', function (array|string $telegramResponse) {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    Http::preventStrayRequests();
    Http::fake(['https://api.telegram.org/*/sendMessage' => Http::response($telegramResponse, 200)]);

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertStatus(503);
})->with([
    'Telegram rejection' => [['ok' => false, 'error_code' => 400]],
    'unexpected response' => ['not-json'],
]);

it('does not send when the configured proxy URL is invalid', function () {
    prepareTelegramSettings(['use_proxy' => true, 'http_proxy' => 'ftp://proxy.example.com']);
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertStatus(503);

    Http::assertNothingSent();
});

it('uses a configured proxy when enabled and ignores the saved proxy URL when disabled', function () {
    config(['services.telegram.bot_token' => 'token-value']);
    prepareTelegramSettings(['use_proxy' => true, 'http_proxy' => 'http://proxy.example.com:3128']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertOk();

    Http::assertSentCount(1);

    Http::fake([
        'https://api.telegram.org/*/sendMessage' => Http::response(['ok' => true], 200),
    ]);
    TelegramSettings::query()->find(1)->update(['use_proxy' => false, 'http_proxy' => 'ftp://must-not-be-used']);

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())->assertOk();

    Http::assertSentCount(1);
});

it('handles an unavailable Telegram connection without an HTTP 500', function (string $failureMessage) {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi(fn () => throw new ConnectionException($failureMessage));

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload())
        ->assertStatus(503)
        ->assertDontSee($failureMessage);
})->with([
    'network failure' => 'network details must not be exposed',
    'request timeout' => 'cURL error 28: Operation timed out',
]);

it('rejects invalid contact details and preserves normal JSON validation errors', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), ['name' => '', 'email' => 'invalid', 'phone' => 'abc'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'phone']);

    Http::assertNothingSent();
});

it('does not send submissions that fill the honeypot field', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    $this->postJson(route('contacts.enquiry'), contactInquiryPayload(['website' => 'spam.example']))
        ->assertUnprocessable()
        ->assertJsonMissingPath('errors.website');

    Http::assertNothingSent();
});

it('limits all inquiry forms to five requests per minute for one IP address', function () {
    prepareTelegramSettings();
    config(['services.telegram.bot_token' => 'token-value']);
    fakeTelegramApi();

    foreach (range(1, 5) as $attempt) {
        $this->postJson(route($attempt % 2 === 0 ? 'contacts.quick' : 'contacts.enquiry'), contactInquiryPayload())->assertOk();
    }

    $this->postJson(route('contacts.quick'), contactInquiryPayload())->assertTooManyRequests();

    Http::assertSentCount(5);
});

it('creates one singleton settings row with a string chat id and boolean proxy state', function () {
    $settings = TelegramSettings::singleton();
    $sameSettings = TelegramSettings::singleton();
    $secondSettings = new TelegramSettings(['chat_id' => '100']);
    $secondSettings->id = 2;

    expect($settings->is($sameSettings))->toBeTrue()
        ->and($settings->use_proxy)->toBeFalse()
        ->and(TelegramSettings::query()->count())->toBe(1)
        ->and(fn () => $secondSettings->save())->toThrow(QueryException::class);
});

it('persists Telegram settings and keeps the proxy address when proxy use is disabled', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(TelegramSettingsPage::class)
        ->fillForm(['chat_id' => '-1001234567890123456', 'use_proxy' => true, 'http_proxy' => 'https://user:pass@proxy.example.com:3128'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(TelegramSettings::query()->find(1)->chat_id)->toBe('-1001234567890123456')
        ->and(TelegramSettings::query()->find(1)->use_proxy)->toBeTrue()
        ->and(TelegramSettings::query()->find(1)->http_proxy)->toBe('https://user:pass@proxy.example.com:3128');

    Livewire::test(TelegramSettingsPage::class)
        ->fillForm(['chat_id' => '-1001234567890123456', 'use_proxy' => false, 'http_proxy' => 'https://user:pass@proxy.example.com:3128'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(TelegramSettings::query()->find(1)->use_proxy)->toBeFalse()
        ->and(TelegramSettings::query()->find(1)->http_proxy)->toBe('https://user:pass@proxy.example.com:3128');
});

it('validates the proxy URL and the chat id in Telegram settings', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(TelegramSettingsPage::class)
        ->fillForm(['chat_id' => '1; DROP TABLE telegram_settings', 'use_proxy' => true, 'http_proxy' => 'ftp://proxy.example.com'])
        ->call('save')
        ->assertHasFormErrors(['chat_id', 'http_proxy']);
});

it('renders active forms with CSRF fields, honeypots, and private configuration omitted', function () {
    config(['services.telegram.bot_token' => 'never-render-this-token']);

    $this->get('/contacts')->assertOk()
        ->assertSee('action="'.route('contacts.enquiry').'"', false)
        ->assertSee('action="'.route('contacts.quick').'"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('name="website"', false)
        ->assertDontSee('never-render-this-token');

    expect(TelegramSettings::query()->count())->toBe(0);
});

it('does not create Telegram settings when a public page is requested', function () {
    $this->get('/')->assertOk();

    expect(TelegramSettings::query()->count())->toBe(0);
});
