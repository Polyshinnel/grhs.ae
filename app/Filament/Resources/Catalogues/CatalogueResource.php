<?php

namespace App\Filament\Resources\Catalogues;

use App\Filament\Resources\Catalogues\Pages\ManageCatalogues;
use App\Models\Catalogue;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CatalogueResource extends Resource
{
    protected static ?string $model = Catalogue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue Library';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catalogue details')->schema([
                Select::make('brand_id')->relationship('brand', 'name')->searchable()->preload()->required(),
                Select::make('category_id')->relationship('category', 'name')->searchable()->preload()->required(),
                Select::make('concepts')->relationship('concepts', 'name')->multiple()->searchable()->preload()->required(),
                FileUpload::make('image_path')->label('Image')->image()->disk('public')->directory('catalogues/images')->visibility('public'),
                TextInput::make('image_alt')->label('Image alt text')->maxLength(255),
                FileUpload::make('original_pdf_path')->label('Original PDF')->acceptedFileTypes(['application/pdf'])->maxSize(1048576)->disk('public')->directory('catalogues/original')->visibility('public'),
                FileUpload::make('compressed_pdf_path')->label('Compressed PDF')->acceptedFileTypes(['application/pdf'])->maxSize(1048576)->disk('public')->directory('catalogues/compressed')->visibility('public'),
                TextInput::make('sort_order')->numeric()->integer()->default(0)->required(),
                Toggle::make('is_published')->label('Published')->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderBy('sort_order')->orderBy('id'))
            ->columns([
                ImageColumn::make('image_path')->disk('public')->label('Image'),
                TextColumn::make('brand.name')->searchable()->sortable()->label('Brand'),
                TextColumn::make('category.name')->searchable()->sortable()->label('Category'),
                TextColumn::make('concepts.name')->badge()->searchable()->label('Concepts'),
                IconColumn::make('original_pdf_path')->boolean(fn (?string $state): bool => filled($state))->label('Original PDF'),
                IconColumn::make('compressed_pdf_path')->boolean(fn (?string $state): bool => filled($state))->label('Compressed PDF'),
                IconColumn::make('is_published')->boolean()->label('Published'),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->label('Updated at'),
            ])
            ->filters([
                SelectFilter::make('brand')->relationship('brand', 'name')->searchable()->preload(),
                SelectFilter::make('category')->relationship('category', 'name')->searchable()->preload(),
                SelectFilter::make('concepts')->relationship('concepts', 'name')->multiple()->searchable()->preload()->label('Concepts'),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCatalogues::route('/')];
    }
}
