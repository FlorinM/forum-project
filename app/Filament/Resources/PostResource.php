<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter; // Correct import for SelectFilter
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth()->user()->can('move', $this->record)) {
            unset($data['thread_id']);
        }

        if (!auth()->user()->can('edit', $this->record)) {
            unset($data['content']);
            unset($data['reported']);
        }

        if (fn () => true) {// No one can change the creator of a post
            unset($data['user_id']);
        }

        if (!auth()->user()->can('approve', $this->record)) {
            unset($data['approved']);
        }

        return $data;
    }

    public static function canCreate(): bool
    {
        // Restrict the "New post" button visibility
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('thread_id')
                ->relationship('thread', 'title')
                ->required()
                ->label('Thread')
                ->visible(fn () => auth()->user()->can('move', Post::class))
                ->disabled(fn () => !auth()->user()->can('move', Post::class)),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->label('User')
                ->visible(fn () => auth()->user()->can('edit', Post::class))
                ->disabled(fn () => true), // No one can change the creator of a post

            Textarea::make('content')
                ->required()
                ->label('Content')
                ->columnSpanFull()
                ->visible(fn () => auth()->user()->can('edit', Post::class))
                ->disabled(fn () => !auth()->user()->can('edit', Post::class)),

            Forms\Components\Checkbox::make('approved')
                ->label('Approved')
                ->visible(fn () => auth()->user()->can('approve', Post::class))
                ->disabled(fn () => !auth()->user()->can('approve', Post::class)),

            Forms\Components\Checkbox::make('reported')
                ->label('Reported')
                ->visible(fn () => auth()->user()->can('edit', Post::class))
                ->disabled(fn () => !auth()->user()->can('edit', Post::class)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('thread.title')
                ->label('Thread')
                ->sortable()
                ->searchable(),

            TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),

            TextColumn::make('content')
                ->label('Content')
                ->limit(50)
                ->wrap(),

            BooleanColumn::make('approved')  // Use BooleanColumn to display boolean values
                ->label('Approved')
                ->sortable(),

            BooleanColumn::make('reported')  // Use BooleanColumn to display boolean values
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
            SelectFilter::make('approved') // Use SelectFilter for boolean-like filtering
                ->label('Approved')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(0),  // Default state (filter for "No")

            SelectFilter::make('reported') // Use SelectFilter for boolean-like filtering
                ->label('Reported')
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->default(0),  // Default state (filter for "No")

            SelectFilter::make('thread_id')
                ->relationship('thread', 'title')
                ->label('By Thread'),

            SelectFilter::make('user_id')
                ->relationship('user', 'name')
                ->label('By User'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn ($record) => auth()->user()->can('edit', $record))
                    ->disabled(fn ($record) => !auth()->user()->can('edit', $record)),

                    // A Visit action, that will open a new tab with
                    // the post at its place in forum
                    Tables\Actions\Action::make('visit')
                    ->label('Visit')
                    ->icon('heroicon-o-link') // Add an icon for better UI
                    ->url(fn ($record) => route('find.post', $record->id)) // Generate the URL dynamically
                    ->openUrlInNewTab(), // Opens the link in a new tab
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
