<?php

namespace App\Filament\Resources\BrandPages;

use App\Filament\Resources\BrandPages\Pages\ManageBrandPages;
use App\Models\BrandPage;
use App\Rules\AvailablePublicPath;
use App\Rules\ValidPublicPath;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BrandPageResource extends Resource
{
    protected static ?string $model = BrandPage::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Pages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Main')
                    ->schema([
                        Select::make('brand_id')->relationship('brand', 'name')->searchable()->preload()->required(),
                        Select::make('category_id')->relationship('category', 'name')->searchable()->preload()->nullable(),
                        TextInput::make('public_path')->required()->maxLength(512)->rules(fn (?Model $record): array => [new ValidPublicPath, new AvailablePublicPath($record)])->helperText('Use a full path beginning with /, such as /tableware/kenai.'),
                        TextInput::make('brand_name')->label('Display name')->maxLength(255),
                        FileUpload::make('hero_image_path')->label('Hero image')->image()->disk('public')->directory('brand-pages')->visibility('public'),
                        TextInput::make('hero_image_alt')->label('Hero image alt text')->maxLength(255),
                        FileUpload::make('category_image_path')->label('Category image')->image()->disk('public')->directory('brand-pages')->visibility('public'),
                        TextInput::make('category_image_alt')->label('Category image alt text')->maxLength(255),
                        FileUpload::make('logo_path')->label('Brand logo')->image()->disk('public')->directory('brand-pages')->visibility('public'),
                        TextInput::make('logo_alt')->label('Brand logo alt text')->maxLength(255),
                        FileUpload::make('catalogue_file_path')->label('Catalogue PDF')->acceptedFileTypes(['application/pdf'])->disk('public')->directory('brand-pages/catalogues')->visibility('public'),
                        Toggle::make('header_black')->default(false),
                        Toggle::make('is_published')->label('Published')->default(false),
                        TextInput::make('sort_order')->numeric()->integer()->default(0)->required(),
                    ])->columns(2),
                Section::make('Content blocks')
                    ->schema([
                        Repeater::make('content_blocks')
                            ->schema([
                                TextInput::make('heading')->maxLength(255),
                                Textarea::make('text')->required()->rows(5)->columnSpanFull(),
                                FileUpload::make('image_path')->label('Image')->image()->disk('public')->directory('brand-pages')->visibility('public'),
                                TextInput::make('image_alt')->label('Image alt text'),
                                Select::make('direction')->options(['normal' => 'Text left, image right', 'reverse' => 'Image left, text right'])->default('normal')->required(),
                            ])
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => $state['heading'] ?? null)
                            ->addActionLabel('Add content block')
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(255),
                        Textarea::make('seo_description')->rows(3),
                        FileUpload::make('og_image_path')->label('OG image')->image()->disk('public')->directory('brand-pages')->visibility('public'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand.name')->searchable()->sortable(),
                TextColumn::make('category.name')->searchable()->sortable(),
                TextColumn::make('public_path')->searchable()->copyable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('brand')->relationship('brand', 'name')->searchable()->preload(),
                SelectFilter::make('category')->relationship('category', 'name')->searchable()->preload(),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBrandPages::route('/'),
        ];
    }
}
