<?php

namespace App\Filament\Resources\CategoryPages;

use App\Filament\Resources\CategoryPages\Pages\ManageCategoryPages;
use App\Models\CategoryPage;
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

class CategoryPageResource extends Resource
{
    protected static ?string $model = CategoryPage::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Pages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Main')
                    ->schema([
                        Select::make('category_id')->relationship('category', 'name')->searchable()->preload()->required(),
                        TextInput::make('public_path')->required()->maxLength(512)->rules(fn (?Model $record): array => [new ValidPublicPath, new AvailablePublicPath($record)])->helperText('Use a full path beginning with /, such as /tableware.'),
                        TextInput::make('hero_heading')->required()->maxLength(255),
                        Textarea::make('hero_text')->rows(4)->columnSpanFull(),
                        FileUpload::make('hero_image_path')->image()->disk('public')->directory('category-pages')->visibility('public'),
                        TextInput::make('hero_image_alt')->label('Image alt text')->maxLength(255),
                        Toggle::make('header_black')->default(false),
                        Toggle::make('is_published')->label('Published')->default(false),
                    ])->columns(2),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(255),
                        Textarea::make('seo_description')->rows(3),
                        FileUpload::make('og_image_path')->label('OG image')->image()->disk('public')->directory('category-pages')->visibility('public'),
                    ])->columns(2),
                Section::make('Text blocks')
                    ->schema([
                        Repeater::make('content_blocks')
                            ->schema([
                                TextInput::make('heading')->label('Heading'),
                                Textarea::make('text')->label('Text')->required()->rows(5),
                            ])
                            ->default([])
                            ->addActionLabel('Add text block')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->searchable()->sortable(),
                TextColumn::make('public_path')->searchable()->copyable(),
                TextColumn::make('hero_heading')->searchable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name')->searchable()->preload(),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategoryPages::route('/'),
        ];
    }
}
