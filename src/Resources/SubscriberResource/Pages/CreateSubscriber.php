<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Filament\Notifications\Notification;

class CreateSubscriber extends CreateRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function afterCreate(): void
    {
        try {
            $this->record->syncWithMailerLite();
            
            Notification::make()
                ->title('Subscriber created and synced with MailerLite!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Subscriber created but sync failed')
                ->body('The subscriber was created locally but could not be synced with MailerLite: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }
}
