<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Filament\Notifications\Notification;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importFromMailerLite')
                ->label('Import from MailerLite')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('info')
                ->action(function () {
                    try {
                        // This is a placeholder - you would implement the actual import logic
                        // $subscribers = MailerLite::subscribers()->get();
                        // foreach ($subscribers as $subscriberData) {
                        //     Subscriber::createFromMailerLite($subscriberData);
                        // }
                        
                        Notification::make()
                            ->title('Import functionality')
                            ->body('Import from MailerLite feature will be implemented based on your specific needs.')
                            ->info()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Import Subscribers from MailerLite')
                ->modalDescription('This will import all subscribers from your MailerLite account. Existing subscribers will be updated.')
                ->modalSubmitActionLabel('Import'),
        ];
    }
}
