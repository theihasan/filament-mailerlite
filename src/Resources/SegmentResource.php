<?php

namespace Ihasan\FilamentMailerLite\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Ihasan\FilamentMailerLite\Models\Segment;
use Ihasan\FilamentMailerLite\Resources\SegmentResource\Pages;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class SegmentResource extends Resource
{
    protected static ?string $model = Segment::class;

    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = 'Segments';

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-mailerlite.resources.segments', true);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\KeyValue::make('rules')->keyLabel('Rule')->valueLabel('Value')->reorderable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $icons = config('filament-mailerlite.icons.actions');
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('mailerlite_id')->label('ML ID')->copyable(),
            Tables\Columns\TextColumn::make('total')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ])->actions([
            Tables\Actions\EditAction::make()->icon($icons['edit'] ?? 'heroicon-o-pencil-square'),
            Tables\Actions\Action::make('sync')
                ->label('Sync to MailerLite')
                ->icon($icons['sync'] ?? 'heroicon-o-arrow-path')
                ->action(function (Segment $record) {
                    try {
                        $record->syncWithMailerLite();
                        Notification::make()->title('Segment synced successfully')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Sync failed')->body($e->getMessage())->danger()->send();
                    }
                }),
            Tables\Actions\DeleteAction::make()
                ->icon($icons['delete'] ?? 'heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Segment')
                ->modalDescription(function (Segment $record) {
                    $description = "Are you sure you want to delete the segment '{$record->name}'?";
                    
                    if ($record->mailerlite_id) {
                        $description .= "\n\nThis will also delete the segment from MailerLite and cannot be undone.";
                    } else {
                        $description .= "\n\nThis segment only exists locally and has not been synced to MailerLite.";
                    }
                    
                    return $description;
                })
                ->modalSubmitActionLabel('Yes, Delete Segment')
                ->modalCancelActionLabel('Cancel')
                ->successNotification(
                    Notification::make()
                        ->title('Segment deleted successfully')
                        ->body(function (Segment $record) {
                            if ($record->mailerlite_id) {
                                return "The segment '{$record->name}' has been deleted from both the local database and MailerLite.";
                            } else {
                                return "The segment '{$record->name}' has been deleted from the local database.";
                            }
                        })
                        ->success()
                )
                ->before(function (Segment $record) {
                    // Delete from MailerLite first if it exists there
                    if ($record->mailerlite_id) {
                        try {
                            MailerLite::segments()->delete($record->mailerlite_id);
                        } catch (\Throwable $e) {
                            // If MailerLite deletion fails, we still want to delete locally
                            // but we should notify the user
                            Notification::make()
                                ->title('Warning: MailerLite deletion failed')
                                ->body("The segment was deleted locally but could not be deleted from MailerLite: {$e->getMessage()}")
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }
                }),
        ])->headerActions([
            Tables\Actions\CreateAction::make()->icon($icons['create'] ?? 'heroicon-o-plus-circle'),
            Tables\Actions\Action::make('import')
                ->label('Import from MailerLite')
                ->icon($icons['import'] ?? 'heroicon-o-cloud-arrow-down')
                ->action(function () {
                    try {
                        $resp = MailerLite::segments()->list();
                        $items = $resp['data'] ?? [];
                        foreach ($items as $s) {
                            Segment::updateOrCreate(
                                ['mailerlite_id' => $s['id'] ?? null],
                                [
                                    'name' => $s['name'] ?? null,
                                    'total' => $s['total'] ?? 0,
                                    'rules' => $s['rules'] ?? [],
                                ]
                            );
                        }
                        Notification::make()->title('Segments imported')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Import failed')->body($e->getMessage())->danger()->send();
                    }
                }),
        ]);
    }

    public static function getPages(): array
    {
        if (static::$navigationIcon === null) {
            static::$navigationIcon = config('filament-mailerlite.icons.segments_navigation', 'heroicon-o-squares-2x2');
        }
        return [
            'index' => Pages\ListSegments::route('/'),
            'create' => Pages\CreateSegment::route('/create'),
            'edit' => Pages\EditSegment::route('/{record}/edit'),
        ];
    }
}


