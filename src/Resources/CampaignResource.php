<?php

namespace Ihasan\FilamentMailerLite\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Ihasan\FilamentMailerLite\Models\Campaign;
use Ihasan\FilamentMailerLite\Resources\CampaignResource\Pages;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = 'Campaigns';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-mailerlite.resources.campaigns', false);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Campaign Details')
                ->icon('heroicon-o-megaphone')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('subject')->maxLength(255),
                    Forms\Components\TextInput::make('from_name')->maxLength(255),
                    Forms\Components\TextInput::make('from_email')->email()->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'sent' => 'Sent',
                            'cancelled' => 'Cancelled',
                        ])->default('draft'),
                    Forms\Components\DateTimePicker::make('send_at')->native(false),
                ])->columns(2),

            Forms\Components\Section::make('Audience')
                ->icon('heroicon-o-users')
                ->schema([
                    Forms\Components\KeyValue::make('groups')->keyLabel('Group ID')->valueLabel('Name')->reorderable(),
                    Forms\Components\KeyValue::make('segments')->keyLabel('Segment ID')->valueLabel('Name')->reorderable(),
                ])->collapsible(),

            Forms\Components\Section::make('Content & Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\KeyValue::make('settings')->columnSpanFull(),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $icons = config('filament-mailerlite.icons.actions');
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('subject')->toggleable(),
            Tables\Columns\TextColumn::make('status')->badge()->sortable(),
            Tables\Columns\TextColumn::make('send_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ])->actions([
            Tables\Actions\ViewAction::make()->icon($icons['view'] ?? 'heroicon-o-eye'),
            Tables\Actions\EditAction::make()->icon($icons['edit'] ?? 'heroicon-o-pencil-square'),
            Tables\Actions\Action::make('sendNow')
                ->label('Send Now')
                ->icon($icons['send'] ?? 'heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn($record) => static::sendCampaign($record)),
            Tables\Actions\Action::make('schedule')
                ->label('Schedule')
                ->icon($icons['schedule'] ?? 'heroicon-o-calendar-days')
                ->form([
                    Forms\Components\DateTimePicker::make('send_at')->required()->native(false),
                ])
                ->action(function ($record, array $data) { $record->update(['send_at' => $data['send_at']]); static::scheduleCampaign($record); }),
            Tables\Actions\DeleteAction::make()->icon($icons['delete'] ?? 'heroicon-o-trash'),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Overview')->schema([
                Infolists\Components\TextEntry::make('name')->icon('heroicon-o-megaphone')->weight('bold'),
                Infolists\Components\TextEntry::make('subject')->icon('heroicon-o-envelope'),
                Infolists\Components\TextEntry::make('status')->badge(),
                Infolists\Components\TextEntry::make('send_at')->dateTime()->icon('heroicon-o-calendar-days'),
            ])->columns(2),
            Infolists\Components\Section::make('Audience')->schema([
                Infolists\Components\KeyValueEntry::make('groups')->columnSpanFull(),
                Infolists\Components\KeyValueEntry::make('segments')->columnSpanFull(),
            ])->collapsible(),
            Infolists\Components\Section::make('Settings & Stats')->schema([
                Infolists\Components\KeyValueEntry::make('settings')->columnSpanFull(),
                Infolists\Components\KeyValueEntry::make('stats')->columnSpanFull(),
            ])->collapsible(),
        ]);
    }

    public static function getPages(): array
    {
        if (static::$navigationIcon === null) {
            static::$navigationIcon = config('filament-mailerlite.icons.campaign_navigation', 'heroicon-o-megaphone');
        }
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'view' => Pages\ViewCampaign::route('/{record}'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    protected static function sendCampaign($record): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Send triggered')
            ->body('Implement send via MailerLite API')
            ->success()
            ->send();
    }

    protected static function scheduleCampaign($record): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Schedule updated')
            ->body('Implement schedule via MailerLite API')
            ->success()
            ->send();
    }
}


