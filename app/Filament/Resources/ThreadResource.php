<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThreadResource\Pages;
use App\Models\Thread;
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

class ThreadResource extends Resource
{
    protected static ?string $model = Thread::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth()->user()->can('move', $this->record)) {
            unset($data['category_id']);
        }

        if (!auth()->user()->can('edit', $this->record)) {
            unset($data['user_id']);
            unset($data['title']);
            unset($data['content']);
            unset($data['reported']);
        }

        if (!auth()->user()-can('approve', $this->record)) {
            unset($data['approved']);
        }

        return $data;
    }

    public static function canCreate(): bool
    {
        // Restrict the "New thread" button visibility
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('category_id')
                ->relationship('category', 'name')
                ->required()
                ->label('Category')
                ->visible(fn () => auth()->user()->can('move', Thread::class))
                ->disabled(fn () => !auth()->user()->can('move', Thread::class)),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->label('User')
                ->visible(fn () => auth()->user()->can('edit', Thread::class))
                ->disabled(fn () => !auth()->user()->can('edit', Thread::class)),

            TextInput::make('title')
                ->required()
                ->label('Title')
                ->visible(fn () => auth()->user()->can('edit', Thread::class))
                ->disabled(fn () => !auth()->user()->can('edit', Thread::class)),

            Textarea::make('content')
                ->required()
                ->label('Content')
                ->columnSpanFull()
                ->visible(fn () => auth()->user()->can('edit', Thread::class))
                ->disabled(fn () => !auth()->user()->can('edit', Thread::class)),

            Forms\Components\Checkbox::make('approved')
                ->label('Approved')
                ->visible(fn () => auth()->user()->can('approve', Thread::class))
                ->disabled(fn () => !auth()->user()->can('approve', Thread::class)),

            Forms\Components\Checkbox::make('reported')
                ->label('Reported')
                ->visible(fn () => auth()->user()->can('edit', Thread::class))
                ->disabled(fn () => !auth()->user()->can('edit', Thread::class)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('category.name')
                ->label('Category')
                ->sortable()
                ->searchable(),

            TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),

            TextColumn::make('title')
                ->label('Title')
                ->sortable()
                ->searchable()
                ->limit(50)
                ->wrap(),

            BooleanColumn::make('approved')
                ->label('Approved')
                ->sortable(),

            BooleanColumn::make('reported')
                ->label('Reported')
                ->sortable(),

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
            SelectFilter::make('approved')
                ->label('Approved')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
            ])
            ->default(0),

            SelectFilter::make('reported')
                ->label('Reported')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(0),

            SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->label('By Category'),

            SelectFilter::make('user_id')
                ->relationship('user', 'name')
                ->label('By User'),
        ])
        ->actions([
            EditAction::make()
                ->visible(fn ($record) => auth()->user()->can('edit', $record))
                ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ])
        ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThreads::route('/'),
            'create' => Pages\CreateThread::route('/create'),
            'edit' => Pages\EditThread::route('/{record}/edit'),
        ];
    }
}
