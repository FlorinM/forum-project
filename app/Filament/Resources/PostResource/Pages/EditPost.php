<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('delete', $this->record))
                ->before(function () {
                    if (!auth()->user()->can('delete', $this->record)) {
                        abort(403, 'You are not authorized to delete this post.');
                    }
            }),

            // Add a "Visit Post" action here
            Actions\Action::make('visit')
            ->label('Visit Post')
            ->url(fn () => route('find.post', $this->record->id)) // Dynamic URL to the post
            ->icon('heroicon-o-link') // Use an external link icon
            ->openUrlInNewTab(), // Opens the link in a new tab
        ];
    }

    protected function beforeSave (): void
    {
        if (!auth()->user()->can('edit', $this->record)) {
            abort(403, 'You are not authorized to edit this post.');
        }
    }
}
