<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('solve', $this->record))
                ->before(function () {
                    if (!auth()->user()->can('solve', $this->record)) {
                        abort(403, 'You are not authorized to delete this record.');
                    }
                }),

            // Add "Edit Post" button
            Actions\Action::make('edit_post')
                ->label('Edit Post')
                ->url(fn () => $this->record->post ? PostResource::getUrl('edit', ['record' => $this->record->post->id]) : '#')
                ->icon('heroicon-o-pencil-square') // Edit icon
                ->visible(fn () => auth()->user()->can('solve', $this->record))
                ->openUrlInNewTab(), // Open in new tab

            // Add a "Visit Post" action here
            Actions\Action::make('visit')
                ->label('Visit Post')
                ->url(fn () => $this->record->post ? route('find.post', $this->record->post->id) : '#')
                ->icon('heroicon-o-link') // Use an external link icon
                ->openUrlInNewTab(), // Opens the link in a new tab
        ];
    }

    protected function beforeSave (): void
    {
        if (!auth()->user()->can('solve', $this->record)) {
            abort(403, 'You are not authorized to solve this report.');
        }
    }
}
