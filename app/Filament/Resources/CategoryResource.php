<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth->user()->can('move', $this->record)) {
            unset($data['parent_id']);
        }

        if (!auth->user()->can('edit', $this->record)) {
            unset($data['user_id']);
            unset($data['name']);
            unset($data['description']);
        }

        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('parent_id')
                ->relationship('parent', 'name')
                ->nullable()
                ->label('Parent Category')
                ->visible(fn ($record) => auth()->user()->can('move', $record))
                ->disabled(fn ($record) => !auth()->user()->can('move', $record)),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->label('User')
                ->visible(fn ($record) => auth()->user()->can('edit', $record))
                ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),

            TextInput::make('name')
                ->required()
                ->label('Category Name')
                ->visible(fn ($record) => auth()->user()->can('edit', $record))
                ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),

            Textarea::make('description')
                ->nullable()
                ->label('Description')
                ->visible(fn ($record) => auth()->user()->can('edit', $record))
                ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('parent.name')
                ->label('Parent Category')
                ->sortable()
                ->searchable(),

            TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),

            TextColumn::make('name')
                ->label('Category Name')
                ->sortable()
                ->searchable(),

            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable(),

            TextColumn::make('updated_at')
                ->label('Updated At')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('user_id')
                ->relationship('user', 'name')
                ->label('By User'),

            SelectFilter::make('parent_id')
                ->relationship('parent', 'name')
                ->label('By Parent Category'),
        ])
        ->actions([
            EditAction::make()
                ->visible(fn ($record) => auth()->user()->can('edit', $record))
                ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
