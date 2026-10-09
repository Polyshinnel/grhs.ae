<?php

namespace App\Filament\Pages;

use App\Models\CataloguePageSettings as CataloguePageSettingsModel;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CataloguePageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Pages';

    protected static ?string $navigationLabel = 'Catalogue Page Settings';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.catalogue-page-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(CataloguePageSettingsModel::singleton()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page content')->schema([
                    TextInput::make('heading')->required()->maxLength(255),
                    Textarea::make('intro_text')->rows(5)->columnSpanFull(),
                    Toggle::make('header_black')->default(false),
                ])->columns(2),
                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->maxLength(255),
                    Textarea::make('seo_description')->rows(3),
                    FileUpload::make('og_image_path')->label('OG image')->image()->disk('public')->directory('catalogues/page-settings')->visibility('public'),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        CataloguePageSettingsModel::singleton()->update($this->form->getState());

        Notification::make()->success()->title('Catalogue page settings saved.')->send();
    }
}
