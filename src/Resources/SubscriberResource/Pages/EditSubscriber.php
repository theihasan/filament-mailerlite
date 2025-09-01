<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Filament\Notifications\Notification;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('sync')
                ->label('Sync with MailerLite')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    try {
                        $this->record->syncWithMailerLite();
                        
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
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        try {
            $this->record->syncWithMailerLite();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Subscriber updated but sync failed')
                ->body('Changes were saved locally but could not be synced with MailerLite: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }
}
