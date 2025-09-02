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
            Forms\Components\Textarea::make('description')->rows(3),
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
                ->action(function (Group $record) {
                    try {
                        if ($record->mailerlite_id) {
                            MailerLite::groups()->update($record->mailerlite_id, [
                                'name' => $record->name,
                                'description' => $record->description,
                            ]);
                        } else {
                            $created = MailerLite::groups()->create([
                                'name' => $record->name,
                                'description' => $record->description,
                            ]);
                            if (isset($created['id'])) {
                                $record->update(['mailerlite_id' => $created['id']]);
                            }
                        }
                        Notification::make()->title('Group synced')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Sync failed')->body($e->getMessage())->danger()->send();
                    }
                }),
            Tables\Actions\DeleteAction::make()->icon($icons['delete'] ?? 'heroicon-o-trash')
                ->after(function (Group $record) {
                    if ($record->mailerlite_id) {
                        try { MailerLite::groups()->delete($record->mailerlite_id); } catch (\Throwable $e) {}
                    }
                }),
        ])->headerActions([
            Tables\Actions\CreateAction::make()->icon($icons['create'] ?? 'heroicon-o-plus-circle'),
            Tables\Actions\Action::make('import')
                ->label('Import from MailerLite')
                ->icon($icons['import'] ?? 'heroicon-o-cloud-arrow-down')
                ->action(function () {
                    try {
                        $resp = MailerLite::groups()->list();
                        $items = $resp['data'] ?? [];
                        foreach ($items as $g) {
                            Group::updateOrCreate(
                                ['mailerlite_id' => $g['id'] ?? null],
                                [
                                    'name' => $g['name'] ?? null,
                                    'description' => $g['description'] ?? null,
                                    'total' => $g['total'] ?? 0,
                                ]
                            );
                        }
                        Notification::make()->title('Groups imported')->success()->send();
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
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}


