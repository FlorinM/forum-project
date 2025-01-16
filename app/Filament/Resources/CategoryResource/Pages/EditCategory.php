<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->can('delete', $this->record))
                ->before(function () {
                    if (!auth()->user()->can('delete', $this->record)) {
                        abort(403, 'You are not authorized to delete this category.');
                    }
                }),
        ];
    }
}
