<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Reports';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth()->user()->can('solve', $this->record)) {
            unset($data['post_id']);
            unset($data['reporter_id']);
            unset($data['content']);
            unset($data['status']);
            unset($data['decision_reason']);
        }

        return $data;
    }

    public static function canCreate(): bool
    {
        // Restrict the "New report" button visibility
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('reporter_id')
                ->label('Reporter')
                ->relationship('reporter', 'name')
                ->required()
                ->visible(fn ($record) => auth()->user()->can('solve', $record))
                ->disabled(),

            Textarea::make('content')
                ->label('Report Content')
                ->required()
                ->visible(fn ($record) => auth()->user()->can('solve', $record))
                ->disabled(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->visible(fn ($record) => auth()->user()->can('solve', $record))
                ->disabled(fn ($record) => !auth()->user()->can('solve', $record))
                ->required(),

            Textarea::make('decision_reason')
                ->label('Decision Reason')
                ->visible(fn ($record) => auth()->user()->can('solve', $record))
                ->disabled(fn ($record) => !auth()->user()->can('solve', $record))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('post.content')
                ->label('Post')
                ->limit(50)
                ->wrap()
                ->searchable(),
            TextColumn::make('reporter.name')->label('Reporter')->searchable(),
            TextColumn::make('post.user.name')->label('Reported')->searchable(),
            TextColumn::make('status')->sortable()->searchable(),
            TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn ($record) => auth()->user()->can('solve', $record))
                    ->disabled(fn ($record) => !auth()->user()->can('solve', $record)),
                DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()->can('solve', $record))
                    ->disabled(fn ($record) => !auth()->user()->can('solve', $record)),

                // A Visit action, that will open a new tab with
                // the post at its place in forum
                Tables\Actions\Action::make('visit')
                    ->label('Visit Post')
                    ->icon('heroicon-o-link') // Add an icon for better UI
                    ->url(fn ($record) => route('find.post', $record->post()->withTrashed()->first()?->id))
                        // Generate the URL dynamically
                    ->visible(fn ($record) => $record->post && !$record->post->trashed())
                        // Only show if the post exists and is not soft deleted
                    ->openUrlInNewTab(), // Opens the link in a new tab
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
