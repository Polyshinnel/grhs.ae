<?php

namespace App\Filament\Pages;

use App\Models\TelegramSettings as TelegramSettingsModel;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TelegramSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Telegram Settings';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.contact-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(TelegramSettingsModel::singleton()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Telegram Bot Notifications')->schema([
                    TextInput::make('chat_id')->label('Chat ID')->nullable()->maxLength(20)
                        ->rule('regex:/^-?[1-9][0-9]{0,19}$/')
                        ->helperText('Enter a numeric user or group ID. Group IDs may start with a minus sign.'),
                    Toggle::make('use_proxy')->label('Use Proxy')->default(false)->live(),
                    TextInput::make('http_proxy')->label('HTTP Proxy')->placeholder('http://127.0.0.1:3128')
                        ->visible(fn (Get $get): bool => (bool) $get('use_proxy'))
                        ->dehydrated()
                        ->required(fn (Get $get): bool => (bool) $get('use_proxy'))
                        ->rules(['url:http,https'])->maxLength(2048),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        TelegramSettingsModel::singleton()->update($this->form->getState());

        Notification::make()->success()->title('Telegram settings saved.')->send();
    }
}
