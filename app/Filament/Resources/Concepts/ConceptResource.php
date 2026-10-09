<?php

namespace App\Filament\Resources\Concepts;

use App\Filament\Resources\Concepts\Pages\ManageConcepts;
use App\Models\Concept;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConceptResource extends Resource
{
    protected static ?string $model = Concept::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Directories';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Concept details')->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('sort_order')->numeric()->integer()->default(0)->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable(),
                TextColumn::make('catalogues_count')->counts('catalogues')->label('Catalogues')->sortable(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Concept $record): bool => $record->catalogues()->exists())
                    ->tooltip('Concepts used by catalogues cannot be deleted.'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageConcepts::route('/')];
    }
}
