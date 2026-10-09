<?php

use App\Filament\Pages\ContactSettings as ContactSettingsPage;
use App\Models\ContactSettings;
use App\Models\User;
use Database\Seeders\ContactSettingsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the contacts page with the specified headings and SEO metadata', function () {
    $response = $this->get('/contacts')->assertOk();
    $content = $response->getContent();

    expect(substr_count($content, '<h1'))->toBe(1);

    $response->assertSee('<h1 id="contacts-title"', false)
        ->assertSee('Contact GRHS</h1>', false)
        ->assertSee('<h2 id="visit-office-title"', false)
        ->assertSee('Visit Our Office in Business Bay, Dubai')
        ->assertSee('<h2 id="how-we-help-title"', false)
        ->assertSee('How We Can Help')
        ->assertSee('<h2 id="get-in-touch-title"', false)
        ->assertSee('Get in Touch')
        ->assertSee('data-header-theme="black"', false)
        ->assertSee('class="relative isolate flex h-svh min-h-svh w-full items-center overflow-hidden bg-grhs-paper text-grhs-ink"', false)
        ->assertSee('src="'.asset('images/home/contacts-hero.jpg').'"', false)
        ->assertSee('GRHS – Golden Ratio Hospitality Supplies – is a Dubai-based supplier of tableware, glassware, cutlery, barware and kitchenware for hotels, restaurants and cafes across the UAE. Visit our office in Ontario Tower, Business Bay, to see samples from our collections and discuss your project with our team.')
        ->assertSee('Contact us to request brand catalogues and prices, order samples, get a proposal for a new opening or refurbishment, or place a repeat order. We work with owners, F&amp;B managers, chefs, purchasing teams and interior designers, and deliver to venues in Dubai, Abu Dhabi and all other Emirates.', false)
        ->assertSee('<title>Contact GRHS – HoReCa Supplier in Business Bay, Dubai</title>', false)
        ->assertSee('<meta name="description" content="GRHS, Office 2203, Ontario Tower, Business Bay, Dubai. Call +971 58 533 8524, WhatsApp or email sales@grhs.ae for catalogues and prices.">', false)
        ->assertSee('<meta property="og:title" content="Contact GRHS – HoReCa Supplier in Business Bay, Dubai">', false)
        ->assertSee('<meta property="og:description" content="GRHS, Office 2203, Ontario Tower, Business Bay, Dubai. Call +971 58 533 8524, WhatsApp or email sales@grhs.ae for catalogues and prices.">', false)
        ->assertSee('<link rel="canonical" href="https://grhs.ae/contacts">', false)
        ->assertSee('<meta property="og:image" content="'.asset('images/home/main-poster.jpg').'">', false);
});

it('renders contact details from settings with telephone email and ordered social links', function () {
    ContactSettings::query()->forceCreate([
        'id' => 1,
        'address' => 'Custom office address',
        'phone' => '+971 50 111 2233',
        'email' => 'hello@example.com',
        'social_links' => [
            ['platform' => 'instagram', 'text' => '@first', 'url' => 'https://www.instagram.com/first/'],
            ['platform' => 'whatsapp', 'text' => 'Chat with us', 'url' => 'https://wa.me/971501112233'],
        ],
    ]);

    $response = $this->get('/contacts')->assertOk()
        ->assertSee('Custom office address')
        ->assertSee('href="tel:+971501112233"', false)
        ->assertSee('href="mailto:hello@example.com"', false)
        ->assertSee('href="https://www.instagram.com/first/" target="_blank" rel="noopener noreferrer"', false)
        ->assertSee('href="https://wa.me/971501112233" target="_blank" rel="noopener noreferrer"', false)
        ->assertSee('@first')
        ->assertSee('Chat with us');

    expect(strpos($response->getContent(), 'https://www.instagram.com/first/'))
        ->toBeLessThan(strpos($response->getContent(), 'https://wa.me/971501112233'));
});

it('reuses configured contact details in the shared footer on other pages', function () {
    ContactSettings::query()->forceCreate([
        'id' => 1,
        'address' => 'Footer office address',
        'phone' => '+971 50 111 2233',
        'email' => 'footer@example.com',
        'social_links' => [
            ['platform' => 'instagram', 'text' => '@footer-account', 'url' => 'https://www.instagram.com/footer-account/'],
        ],
    ]);

    $this->get('/')->assertOk()
        ->assertSee('Footer office address')
        ->assertSee('href="tel:+971501112233"', false)
        ->assertSee('href="mailto:footer@example.com"', false)
        ->assertSee('href="https://www.instagram.com/footer-account/" target="_blank" rel="noopener noreferrer"', false)
        ->assertDontSee('Office 2203, 22th floor, Ontario Tower');
});

it('renders safely when contact settings or social links are absent', function () {
    $response = $this->get('/contacts')->assertOk();
    $contactSection = Str::between(
        $response->getContent(),
        '<section class="bg-grhs-sand/50',
        '<section class="px-5 py-14 sm:px-8 sm:py-20',
    );

    expect($contactSection)->not->toContain('https://wa.me/');

    ContactSettings::query()->forceCreate(['id' => 1, 'social_links' => null]);

    $response = $this->get('/contacts')->assertOk();
    $contactSection = Str::between(
        $response->getContent(),
        '<section class="bg-grhs-sand/50',
        '<section class="px-5 py-14 sm:px-8 sm:py-20',
    );

    expect($contactSection)
        ->not->toContain('https://wa.me/')
        ->not->toContain('instagram/');
});

it('omits the map when coordinates are absent and shows it when configured with a Mapbox token', function () {
    ContactSettings::query()->forceCreate(['id' => 1, 'map_latitude' => null, 'map_longitude' => null]);

    $this->get('/contacts')->assertOk()
        ->assertDontSee('data-contact-map', false)
        ->assertSee('The office map is currently unavailable.');

    ContactSettings::query()->whereKey(1)->update([
        'map_latitude' => 25.1858999,
        'map_longitude' => 55.2621375,
    ]);
    config(['services.mapbox.public_token' => 'pk.example-token']);

    $this->get('/contacts')->assertOk()
        ->assertSee('data-contact-map', false)
        ->assertSee('data-latitude="25.1858999"', false)
        ->assertSee('data-longitude="55.2621375"', false)
        ->assertSee('data-mapbox-token="pk.example-token"', false);
});

it('renders the active contact form with its enquiry endpoint', function () {
    $this->get('/contacts')->assertOk()
        ->assertSee('action="'.route('contacts.enquiry').'"', false)
        ->assertSee('id="contacts-form-status"', false)
        ->assertSee('name="website"', false)
        ->assertSee('<button type="submit"', false)
        ->assertDontSee('Message sent successfully');

    $this->post('/contacts')->assertMethodNotAllowed();
    expect(ContactSettings::query()->count())->toBe(0);
});

it('returns the same singleton settings record without creating duplicates', function () {
    $first = ContactSettings::singleton();
    $second = ContactSettings::singleton();
    $additionalSettings = new ContactSettings(['address' => 'A second settings row']);
    $additionalSettings->setAttribute('id', 2);
    expect(fn () => $additionalSettings->save())->toThrow(QueryException::class);

    expect($first->is($second))->toBeTrue()
        ->and(ContactSettings::query()->count())->toBe(1)
        ->and(ContactSettings::query()->first()->address)->toBe($first->address);
});

it('saves, orders, and restores social links in the Filament repeater', function () {
    $socialLinks = [
        ['platform' => 'whatsapp', 'text' => 'Main number', 'url' => 'https://wa.me/971585338524'],
        ['platform' => 'instagram', 'text' => '@grhs_uae', 'url' => 'https://www.instagram.com/grhs_uae/'],
    ];

    Livewire::test(ContactSettingsPage::class)
        ->fillForm([
            'address' => 'Office 2203, Ontario Tower',
            'phone' => '+971 58 533 8524',
            'email' => 'sales@grhs.ae',
            'social_links' => $socialLinks,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $storedSocialLinks = ContactSettings::query()->find(1)->social_links;

    expect($storedSocialLinks)->toHaveCount(2)
        ->and($storedSocialLinks[0])->toMatchArray($socialLinks[0])
        ->and($storedSocialLinks[1])->toMatchArray($socialLinks[1]);

    $restoredSocialLinks = array_values(
        Livewire::test(ContactSettingsPage::class)->get('data.social_links'),
    );

    expect($restoredSocialLinks)->toHaveCount(2)
        ->and($restoredSocialLinks[0])->toMatchArray($socialLinks[0])
        ->and($restoredSocialLinks[1])->toMatchArray($socialLinks[1]);
});

it('shows the Contact Settings page in the authenticated Filament panel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/contact-settings')
        ->assertOk()
        ->assertSee('Contact Information')
        ->assertSee('Social Links')
        ->assertSee('Add social link');
});

it('seeds default contacts once and preserves later user edits', function () {
    $seeder = new ContactSettingsSeeder;
    $seeder->run();

    $settings = ContactSettings::query()->find(1);
    expect($settings->address)->toBe('Office 2203, 22nd floor, Ontario Tower, Business Bay, Dubai, UAE')
        ->and($settings->phone)->toBe('+971 58 533 8524')
        ->and($settings->email)->toBe('sales@grhs.ae')
        ->and($settings->social_links)->toHaveCount(2);

    $settings->update(['address' => 'Updated office address']);
    $seeder->run();

    expect(ContactSettings::query()->count())->toBe(1)
        ->and($settings->refresh()->address)->toBe('Updated office address');
});
