<?php

namespace Ihasan\FilamentMailerLite\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Ihasan\FilamentMailerLite\Models\Subscriber;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ihasan\FilamentMailerLite\Enums\SubscriberStatus;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Subscribers';

    protected static ?string $modelLabel = 'Subscriber';

    protected static ?string $pluralModelLabel = 'Subscribers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->options(SubscriberStatus::options())
                            ->default('active')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('source')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Custom Fields')
                    ->schema([
                        Forms\Components\KeyValue::make('fields')
                            ->keyLabel('Field Name')
                            ->valueLabel('Field Value')
                            ->addActionLabel('Add Custom Field')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('sent')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('opens_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('clicks_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('open_rate')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('click_rate')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Dates & Timeline')
                    ->schema([
                        Forms\Components\DatePicker::make('subscribed_at')
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('unsubscribed_at')
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('opted_in_at')
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('optin_ip')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied to clipboard'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No name'),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('sent')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('opens_count')
                    ->label('Opens')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Open Rate')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('formatted_location')
                    ->label('Location')
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('subscribed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubscriberStatus::options()),
                Tables\Filters\Filter::make('subscribed_recently')
                    ->query(fn (Builder $query): Builder => $query->where('subscribed_at', '>=', now()->subDays(30)))
                    ->label('Subscribed in last 30 days'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Subscriber $record) {
                        try {
                            $record->syncWithMailerLite();
                            Notification::make()
                                ->title('Subscriber synced successfully!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Sync failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('sync_selected')
                        ->label('Sync with MailerLite')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->action(function ($records) {
                            $successful = 0;
                            $failed = 0;
                            
                            collect($records)->each(function ($record) use (&$successful, &$failed) {
                                try {
                                    $record->syncWithMailerLite();
                                    $successful++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            });
                            
                            $message = "Synced {$successful} subscribers successfully.";
                            if ($failed > 0) {
                                $message .= " {$failed} failed.";
                            }
                            
                            Notification::make()
                                ->title($message)
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Subscriber Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('name')
                            ->placeholder('No name provided'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'unsubscribed' => 'danger',
                                'bounced' => 'warning',
                                'junk' => 'gray',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('source')
                            ->placeholder('Unknown'),
                        Infolists\Components\TextEntry::make('mailerlite_id')
                            ->label('MailerLite ID')
                            ->placeholder('Not synced'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('sent')
                            ->label('Emails Sent'),
                        Infolists\Components\TextEntry::make('opens_count')
                            ->label('Total Opens'),
                        Infolists\Components\TextEntry::make('clicks_count')
                            ->label('Total Clicks'),
                        Infolists\Components\TextEntry::make('open_rate')
                            ->label('Open Rate')
                            ->suffix('%'),
                        Infolists\Components\TextEntry::make('click_rate')
                            ->label('Click Rate')
                            ->suffix('%'),
                        Infolists\Components\TextEntry::make('formatted_location')
                            ->label('Location'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Custom Fields')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('fields')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Timeline')
                    ->schema([
                        Infolists\Components\TextEntry::make('subscribed_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('opted_in_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('unsubscribed_at')
                            ->dateTime()
                            ->placeholder('Still subscribed'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'view' => Pages\ViewSubscriber::route('/{record}'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'MailerLite';
    }
}
