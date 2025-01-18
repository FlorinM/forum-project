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
use Tables\Actions\ActionGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth()->user()->can('edit', $this->record)) {
            unset($data['name']);
            unset($data['nickname']);
            unset($data['email']);
            unset($data['avatar_url']);
            unset($data['email_verified_at']);
            unset($data['password']);
            unset($data['birthday']);
            unset($data['gender']);
            unset($data['description']);
            unset($data['signature']);
        }

        if (!auth()->user()->can('ban', $this->record) ||
            !auth()->user()->can('unban', $this->record)) {

            unset($data['is_banned']);
        }

        return $data;
    }

    public static function canCreate(): bool
    {
        // Restrict the "New User" button visibility
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->visible(fn () => auth()->user()->can('edit', User::class))
                ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('nickname')
                 ->required()
                 ->maxLength(255)
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('email')
                 ->email()
                 ->required()
                 ->maxLength(255)
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('avatar_url')
                 ->maxLength(255)
                 ->default(null)
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\DateTimePicker::make('email_verified_at')
                ->visible(fn () => auth()->user()->can('edit', User::class))
                ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('password')
                 ->password()
                 ->required()
                 ->maxLength(255)
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\DatePicker::make('birthday')
                ->visible(fn () => auth()->user()->can('edit', User::class))
                ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('gender')
                ->visible(fn () => auth()->user()->can('edit', User::class))
                ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\Textarea::make('description')
                 ->columnSpanFull()
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\TextInput::make('signature')
                 ->maxLength(255)
                 ->default(null)
                 ->visible(fn () => auth()->user()->can('edit', User::class))
                 ->disabled(fn () => !auth()->user()->can('edit', User::class)),

            Forms\Components\DateTimePicker::make('is_banned')
                ->visible(fn () => auth()->user()->can('ban', User::class) ||
                          fn () => auth()->user()->can('unban', User::class))
                ->disabled(fn () => !auth()->user()->can('ban', User::class) ||
                           fn () => !auth()->user()->can('unban', User::class)
                ),
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
            Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => auth()->user()->can('edit', $record))
                    ->disabled(fn (User $record) => !auth()->user()->can('edit', $record)),

                Tables\Actions\Action::make('ban')
                    ->label('Ban')
                    ->icon('heroicon-o-lock-closed')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('days')
                        ->label('Ban Duration (Days)')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Enter number of days'),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->ban($data['days']); // Use the entered number of days
                    })
                    ->visible(fn (User $record) => !$record->isBanned() &&
                              fn (User $record) => auth()->user()->can('ban', $record))
                    ->disabled(fn (User $record) => !auth()->user()->can('ban', $record)),

                Tables\Actions\Action::make('unban')
                    ->label('Unban')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->unban())
                    ->visible(fn (User $record) => $record->isBanned() &&
                              fn (User $record) => auth()->user()->can('unban', $record))
                    ->disabled(fn (User $record) => !auth()->user()->can('unban', $record)),

                Tables\Actions\Action::make('Promote to Moderator')
                    ->action(fn (User $record) => $record->promoteToModerator())
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn (User $record) => auth()->user()->can('promoteToModerator', $record))
                    ->disabled(fn (User $record) => !auth()->user()->can('promoteToModerator', $record)),


                Tables\Actions\Action::make('Demote to User')
                    ->action(fn (User $record) => $record->demoteToUser())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(fn (User $record) => auth()->user()->can('demoteToUser', $record))
                    ->disabled(fn (User $record) => !auth()->user()->can('demoteToUser', $record)),
            ])
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
