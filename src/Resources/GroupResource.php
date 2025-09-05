<?php

namespace Ihasan\FilamentMailerLite\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Ihasan\FilamentMailerLite\Models\Group;
use Ihasan\FilamentMailerLite\Resources\GroupResource\Pages;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = 'Groups';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->required()->rows(3),
        ]);
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([
            \Filament\Infolists\Components\TextEntry::make('name')
                ->label('Group Name'),
            \Filament\Infolists\Components\TextEntry::make('mailerlite_id')
                ->label('MailerLite ID')
                ->copyable(),
            \Filament\Infolists\Components\TextEntry::make('description')
                ->label('Description'),
            \Filament\Infolists\Components\TextEntry::make('total')
                ->label('Total Subscribers')
                ->numeric(),
            \Filament\Infolists\Components\TextEntry::make('type')
                ->label('Type'),
            \Filament\Infolists\Components\TextEntry::make('created_at')
                ->label('Created At')
                ->dateTime(),
            \Filament\Infolists\Components\TextEntry::make('updated_at')
                ->label('Updated At')
                ->dateTime(),
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
            Tables\Actions\ViewAction::make()
                ->icon($icons['view'] ?? 'heroicon-o-eye'),
            Tables\Actions\EditAction::make()
                ->icon($icons['edit'] ?? 'heroicon-o-pencil-square')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(3),
                ])
                ->using(function (Group $record, array $data): Group {
                    $record->update($data);
                
                    try {
                        $record->syncWithMailerLite();
                        Notification::make()
                            ->title('Group updated and synced')
                            ->body("Group '{$record->name}' was updated and synced with MailerLite.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Updated locally, sync failed')
                            ->body('Could not sync with MailerLite: ' . $e->getMessage())
                            ->warning()
                            ->send();
                    }
                    
                    return $record;
                }),
            Tables\Actions\Action::make('sync')
                ->label('Sync to MailerLite')
                ->icon($icons['sync'] ?? 'heroicon-o-arrow-path')
                ->action(function (Group $record) {
                    try {
                        $record->syncWithMailerLite();
                        Notification::make()->title('Group synced successfully')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Sync failed')->body($e->getMessage())->danger()->send();
                    }
                }),
            Tables\Actions\Action::make('delete')
                ->label('Delete')
                ->icon($icons['delete'] ?? 'heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Group')
                ->modalDescription(function (Group $record) {
                    $description = "Are you sure you want to delete the group '{$record->name}'?";
                    
                    if ($record->mailerlite_id) {
                        $description .= "\n\nThis will also delete the group from MailerLite and cannot be undone.";
                    } else {
                        $description .= "\n\nThis group only exists locally and has not been synced to MailerLite.";
                    }
                    
                    return $description;
                })
                ->modalSubmitActionLabel('Yes, Delete Group')
                ->modalCancelActionLabel('Cancel')
                ->action(function (Group $record) {
                    try {
                        // Delete from MailerLite first if it exists there
                        if ($record->mailerlite_id) {
                            try {
                                $result = MailerLite::groups()->delete((string) $record->mailerlite_id);
                                
                                // Send success notification for MailerLite deletion
                                Notification::make()
                                    ->title('Group deleted from MailerLite')
                                    ->body("The group '{$record->name}' has been successfully deleted from MailerLite.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                // If MailerLite deletion fails, we still want to delete locally
                                // but we should notify the user
                                Notification::make()
                                    ->title('Warning: MailerLite deletion failed')
                                    ->body("The group will be deleted locally but could not be deleted from MailerLite: {$e->getMessage()}")
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            }
                        } else {
                            // Group only exists locally, no MailerLite deletion needed
                            Notification::make()
                                ->title('Local group deletion')
                                ->body("The group '{$record->name}' only exists locally and will be deleted from the database.")
                                ->info()
                                ->send();
                        }
                        
                        // Delete from local database
                        $record->delete();
                        
                        // Send final success notification
                        Notification::make()
                            ->title('Group deleted successfully')
                            ->body(function () use ($record) {
                                if ($record->mailerlite_id) {
                                    return "The group '{$record->name}' has been deleted from both the local database and MailerLite.";
                                } else {
                                    return "The group '{$record->name}' has been deleted from the local database.";
                                }
                            })
                            ->success()
                            ->send();
                            
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Delete failed')
                            ->body("Failed to delete group: {$e->getMessage()}")
                            ->danger()
                            ->send();
                    }
                }),
        ])->headerActions([
            Tables\Actions\CreateAction::make()
                ->icon($icons['create'] ?? 'heroicon-o-plus-circle')
                ->after(function (Group $record) {
                    try {
                        $record->syncWithMailerLite();
                        Notification::make()
                            ->title('Group created and synced')
                            ->body("Group '{$record->name}' was created and synced with MailerLite.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Created locally, sync failed')
                            ->body('Could not sync with MailerLite: ' . $e->getMessage())
                            ->warning()
                            ->send();
                    }
                }),
            Tables\Actions\Action::make('import')
                ->label('Import from MailerLite')
                ->icon($icons['import'] ?? 'heroicon-o-cloud-arrow-down')
                ->action(function () {
                    try {
                        $resp = MailerLite::groups()->list();
                        $items = $resp['data'] ?? [];
                        
                        if (empty($items)) {
                            Notification::make()->title('No groups found')->body('No groups were found in your MailerLite account')->warning()->send();
                            return;
                        }
                        
                        $importedCount = 0;
                        collect($items)
                            ->reject(function ($g) {
                                return empty($g['id']) && empty($g['name']);
                            })
                            ->each(function ($g) use (&$importedCount) {
                                Group::updateOrCreate(
                                    ['mailerlite_id' => $g['id']],
                                    [
                                        'name' => $g['name'],
                                        'description' => $g['description'] ?? null,
                                        'total' => $g['total'] ?? 0,
                                    ]
                                );
                                $importedCount++;
                            });
                        
                        if ($importedCount > 0) {
                            Notification::make()->title('Groups imported successfully')->body("Imported {$importedCount} group(s) from MailerLite")->success()->send();
                        } else {
                            Notification::make()->title('No valid groups found')->body('Found groups but none had valid IDs or names')->warning()->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()->title('Import failed')->body($e->getMessage())->danger()->send();
                    }
                }),
        ]);
    }

    public static function getPages(): array
    {
        if (static::$navigationIcon === null) {
            static::$navigationIcon = config('filament-mailerlite.icons.groups_navigation', 'heroicon-o-rectangle-group');
        }
        return [
            'index' => Pages\ListGroups::route('/'),
           // 'create' => Pages\CreateGroup::route('/create'),
            'view' => Pages\ViewGroup::route('/{record}'),
        ];
    }
}


