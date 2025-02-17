<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Ban Button
            Actions\Action::make('ban')
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
                ->action(function (array $data) {
                    $this->record->ban($data['days']); // Ban the user for the entered duration
                })
                ->visible(fn () => !$this->record->isBanned() && auth()->user()->can('ban', $this->record))
                ->disabled(fn () => !auth()->user()->can('ban', $this->record)),

            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('delete', $this->record))
                ->before(function () {
                    if (!auth()->user()->can('delete', $this->record)) {
                        abort(403, 'You are not authorized to delete this user.');
                    }
                }),
        ];
    }

    protected function beforeSave (): void
    {
        if (!auth()->user()->can('edit', $this->record)) {
            abort(403, 'You are not authorized to edit this user.');
        }
    }
}
