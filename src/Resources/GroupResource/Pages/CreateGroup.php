<?php

namespace Ihasan\FilamentMailerLite\Resources\GroupResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Ihasan\FilamentMailerLite\Resources\GroupResource;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function afterCreate(): void
    {
        try {
            $this->record->syncWithMailerLite();
            
            \Filament\Notifications\Notification::make()
                ->title('Group Created Successfully!')
                ->body("Group '{$this->record->name}' has been created locally and synced with MailerLite.")
                ->success()
                ->duration(5000)
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Partial Success')
                ->body('Group was created locally but could not be synced with MailerLite: ' . $e->getMessage())
                ->warning()
                ->duration(8000)
                ->send();
        }
    }
}


