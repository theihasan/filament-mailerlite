<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class ViewSubscriber extends ViewRecord
{
    protected static string $resource = SubscriberResource::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendTestEmail')
                ->label('Send Test Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->outlined()
                ->action(function () {
                    Notification::make()
                        ->title('📧 Test Email Feature')
                        ->body('Test email functionality can be implemented using the MailerLite API.')
                        ->info()
                        ->send();
                }),
            
            Actions\Action::make('sync')
                ->label('Sync with MailerLite')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    try {
                        $this->record->syncWithMailerLite();
                        
                        Notification::make()
                            ->title('Sync Successful!')
                            ->body("Subscriber {$this->record->email} has been synced with MailerLite.")
                            ->success()
                            ->duration(4000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Sync Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(6000)
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Sync with MailerLite')
                ->modalDescription('This will update the subscriber information in your MailerLite account with the current local data.')
                ->modalSubmitActionLabel('Sync Now'),
            
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),
            
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Subscriber')
                ->modalDescription('Are you sure you want to delete this subscriber? This action cannot be undone and will also remove them from MailerLite.')
                ->successNotificationTitle('Subscriber deleted successfully'),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->name 
            ? "Subscriber: {$this->record->name}" 
            : "Subscriber: {$this->record->email}";
    }
}