<?php

namespace App\Filament\Pages;

use App\Models\ContactSettings as ContactSettingsModel;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Pages';

    protected static ?string $navigationLabel = 'Contact Settings';

    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.pages.contact-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ContactSettingsModel::singleton()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Information')->schema([
                    Textarea::make('address')->label('Address')->nullable()->rows(3)
                        ->helperText('The office address shown on the public contacts page.')
                        ->columnSpanFull(),
                    TextInput::make('phone')->label('Phone')->type('tel')->nullable()
                        ->helperText('Include the country code for click-to-call links.'),
                    TextInput::make('email')->label('Email')->email()->nullable()
                        ->helperText('Shown as a clickable email address.'),
                    TextInput::make('map_latitude')->label('Map Latitude')->numeric()->nullable()
                        ->minValue(-90)->maxValue(90)->step('0.0000001')
                        ->helperText('Latitude from -90 to 90. Leave blank to hide the map.'),
                    TextInput::make('map_longitude')->label('Map Longitude')->numeric()->nullable()
                        ->minValue(-180)->maxValue(180)->step('0.0000001')
                        ->helperText('Longitude from -180 to 180. Leave blank to hide the map.'),
                ])->columns(2),
                Section::make('Social Links')->schema([
                    Repeater::make('social_links')
                        ->label('Social links')
                        ->schema([
                            Select::make('platform')->options([
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'instagram' => 'Instagram',
                                'facebook' => 'Facebook',
                                'linkedin' => 'LinkedIn',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'x' => 'X / Twitter',
                            ])->required(),
                            TextInput::make('text')->label('Display Text')->required()->maxLength(255),
                            TextInput::make('url')->label('URL')->required()
                                ->rules(['url:http,https'])->maxLength(2048),
                        ])
                        ->columns(3)
                        ->addActionLabel('Add social link')
                        ->reorderable()
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        ContactSettingsModel::singleton()->update($this->form->getState());

        Notification::make()->success()->title('Contact settings saved.')->send();
    }
}
