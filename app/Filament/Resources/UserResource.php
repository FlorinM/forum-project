<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('nickname')
                 ->required()
                 ->maxLength(255),
            Forms\Components\TextInput::make('email')
                 ->email()
                 ->required()
                 ->maxLength(255),
            Forms\Components\TextInput::make('avatar_url')
                 ->maxLength(255)
                 ->default(null),
            Forms\Components\DateTimePicker::make('email_verified_at'),
            Forms\Components\TextInput::make('password')
                 ->password()
                 ->required()
                 ->maxLength(255),
            Forms\Components\DatePicker::make('birthday'),
            Forms\Components\TextInput::make('gender'),
            Forms\Components\Textarea::make('description')
                 ->columnSpanFull(),
            Forms\Components\TextInput::make('signature')
                 ->maxLength(255)
                 ->default(null),
            Forms\Components\DateTimePicker::make('is_banned'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('nickname')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('email_verified_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('birthday')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('gender'),
            Tables\Columns\TextColumn::make('is_banned')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('roles.name')
                ->label('Role')
                ->visible(fn () => auth()->user()->hasRole('Admin')),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('ban')
                ->label('Ban')
                ->icon('heroicon-o-lock-closed')
                ->requiresConfirmation()
                ->action(fn (User $record) => $record->ban(30)) // Adjust ban duration as needed
                ->visible(fn (User $record) => !$record->isBanned()),
            Tables\Actions\Action::make('unban')
                ->label('Unban')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(fn (User $record) => $record->unban())
                ->visible(fn (User $record) => $record->isBanned()),
            Tables\Actions\Action::make('Promote to Moderator')
                ->action(fn (User $record) => $record->promoteToModerator())
                ->requiresConfirmation()
                ->color('success')
                ->visible(fn (User $record) => $record->hasRole('User') && auth()->user()->hasRole('Admin')),
            Tables\Actions\Action::make('Demote to User')
                ->action(fn (User $record) => $record->demoteToUser())
                ->requiresConfirmation()
                ->color('danger')
                ->visible(fn (User $record) => $record->hasRole('Moderator') && auth()->user()->hasRole('Admin')),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
