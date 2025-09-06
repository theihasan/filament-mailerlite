<?php

namespace Ihasan\FilamentMailerLite\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SyncGroupAction extends Action
{
    public static function getDefaultName(): string
    {
        return 'sync_group';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Sync Group')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->action(function () {
                try {
                    $this->getRecord()->syncWithMailerLite();
                    Notification::make()
                        ->title('Group synced successfully')
                        ->body("Group '{$this->getRecord()->name}' has been synced with MailerLite.")
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Sync failed')
                        ->body('Failed to sync group: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
