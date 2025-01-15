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

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('thread_id')
                ->relationship('thread', 'title')
                ->required()
                ->label('Thread'),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->label('User'),

            Textarea::make('content')
                ->required()
                ->label('Content')
                ->columnSpanFull(),

            Forms\Components\Checkbox::make('approved')
                ->label('Approved'),

            Forms\Components\Checkbox::make('reported')
                ->label('Reported'),
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
                EditAction::make(),
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
