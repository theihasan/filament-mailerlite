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
                Forms\Components\Section::make('Subscriber Information')
                    ->description('Basic subscriber details and contact information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('subscriber@example.com')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->prefixIcon('heroicon-o-user')
                            ->placeholder('John Doe')
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->options(SubscriberStatus::options())
                            ->default('active')
                            ->prefixIcon('heroicon-o-signal')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('source')
                            ->prefixIcon('heroicon-o-link')
                            ->placeholder('website, api, import')
                            ->helperText('Where did this subscriber come from?')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('🏷️ Custom Fields')
                    ->description('Additional subscriber information and custom data')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\KeyValue::make('fields')
                            ->keyLabel('Field Name')
                            ->valueLabel('Field Value')
                            ->addActionLabel('+ Add Custom Field')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('📊 Performance Statistics')
                    ->description('Email engagement metrics and performance data')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('sent')
                                    ->label('Emails Sent')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-paper-airplane')
                                    ->extraAttributes(['class' => 'text-blue-600']),
                                Forms\Components\TextInput::make('opens_count')
                                    ->label('Total Opens')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-eye')
                                    ->extraAttributes(['class' => 'text-green-600']),
                                Forms\Components\TextInput::make('clicks_count')
                                    ->label('Total Clicks')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-cursor-arrow-rays')
                                    ->extraAttributes(['class' => 'text-purple-600']),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('open_rate')
                                    ->label('Open Rate')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-chart-pie')
                                    ->extraAttributes(['class' => 'text-emerald-600']),
                                Forms\Components\TextInput::make('click_rate')
                                    ->label('Click Rate')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-chart-bar-square')
                                    ->extraAttributes(['class' => 'text-orange-600']),
                                Forms\Components\TextInput::make('ip_address')
                                    ->label('IP Address')
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->placeholder('Unknown'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('📅 Timeline & History')
                    ->description('Important dates and subscription history')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\DateTimePicker::make('subscribed_at')
                            ->label('Subscription Date')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('unsubscribed_at')
                            ->label('Unsubscription Date')
                            ->native(false)
                            ->prefixIcon('heroicon-o-x-circle')
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('opted_in_at')
                            ->label('Opt-in Date')
                            ->native(false)
                            ->prefixIcon('heroicon-o-check-circle')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('optin_ip')
                            ->label('Opt-in IP Address')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->placeholder('IP address when opted in')
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
                Infolists\Components\Section::make('Subscriber Profile')
                    ->description('Core subscriber information and account status')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('Email copied!')
                                    ->weight('bold')
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('name')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('No name provided')
                                    ->weight('semibold'),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->icon(fn (string $state): string => match ($state) {
                                        'active' => 'heroicon-o-check-circle',
                                        'unsubscribed' => 'heroicon-o-x-circle',
                                        'bounced' => 'heroicon-o-exclamation-triangle',
                                        'junk' => 'heroicon-o-trash',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'unsubscribed' => 'danger',
                                        'bounced' => 'warning',
                                        'junk' => 'gray',
                                        default => 'gray',
                                    }),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('source')
                                    ->icon('heroicon-o-link')
                                    ->placeholder('Unknown source')
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('mailerlite_id')
                                    ->label('MailerLite ID')
                                    ->icon('heroicon-o-identification')
                                    ->placeholder('Not synced')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Engagement Analytics')
                    ->description('Email performance metrics and engagement statistics')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('sent')
                                    ->label('Emails Sent')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->badge()
                                    ->color('blue')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('opens_count')
                                    ->label('Total Opens')
                                    ->icon('heroicon-o-eye')
                                    ->badge()
                                    ->color('green')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('clicks_count')
                                    ->label('Total Clicks')
                                    ->icon('heroicon-o-cursor-arrow-rays')
                                    ->badge()
                                    ->color('purple')
                                    ->weight('bold'),
                            ]),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('open_rate')
                                    ->label('Open Rate')
                                    ->suffix('%')
                                    ->icon('heroicon-o-chart-pie')
                                    ->badge()
                                    ->color('emerald')
                                    ->weight('semibold'),
                                Infolists\Components\TextEntry::make('click_rate')
                                    ->label('Click Rate')
                                    ->suffix('%')
                                    ->icon('heroicon-o-chart-bar-square')
                                    ->badge()
                                    ->color('orange')
                                    ->weight('semibold'),
                                Infolists\Components\TextEntry::make('formatted_location')
                                    ->label('Location')
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('Unknown location')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Custom Data')
                    ->description('Additional fields and custom subscriber information')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('fields')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Activity Timeline')
                    ->description('Important dates and subscription milestones')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('subscribed_at')
                                    ->label('Subscription Date')
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar-days')
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('opted_in_at')
                                    ->label('Opt-in Date')
                                    ->dateTime()
                                    ->icon('heroicon-o-check-circle')
                                    ->badge()
                                    ->color('info'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('unsubscribed_at')
                                    ->label('Unsubscription Date')
                                    ->dateTime()
                                    ->icon('heroicon-o-x-circle')
                                    ->placeholder('Still subscribed')
                                    ->badge()
                                    ->color('danger'),
                                Infolists\Components\TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->icon('heroicon-o-globe-alt')
                                    ->placeholder('Unknown')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Record Created')
                                    ->dateTime()
                                    ->icon('heroicon-o-plus-circle')
                                    ->since()
                                    ->color('gray'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime()
                                    ->icon('heroicon-o-pencil-square')
                                    ->since()
                                    ->color('gray'),
                            ]),
                    ]),
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
