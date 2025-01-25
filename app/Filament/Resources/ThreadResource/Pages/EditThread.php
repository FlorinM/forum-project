<?php

namespace App\Filament\Resources\ThreadResource\Pages;

use App\Filament\Resources\ThreadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThread extends EditRecord
{
    protected static string $resource = ThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('delete', $this->record))
                ->before(function () {
                    if (!auth()->user()->can('delete', $this->record)) {
                        abort(403, 'You are not authorized to delete this thread.');
                    }
                }),

            Actions\Action::make('visit')
                ->label('Visit')
                ->url(fn () => route('threads.show', ['category' => $this->record->category->id, 'thread' => $this->record->id]))
                ->openUrlInNewTab()
                ->icon('heroicon-o-link'),
        ];
    }

    protected function beforeSave (): void
    {
        if (!auth()->user()->can('edit', $this->record)) {
            abort(403, 'You are not authorized to edit this thread.');
        }
    }
}
