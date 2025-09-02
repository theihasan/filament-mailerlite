<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class CreateSubscriber extends CreateRecord
{
    protected static string $resource = SubscriberResource::class;

    protected static ?string $title = 'Add New Subscriber';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getCreateFormAction(): Actions\Action
    {
        $icons = config('filament-mailerlite.icons.actions');
        return parent::getCreateFormAction()
            ->label('Create & Sync with MailerLite')
            ->icon($icons['create'] ?? 'heroicon-o-plus-circle')
            ->color('success');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        $icons = config('filament-mailerlite.icons.actions');
        return parent::getCancelFormAction()
            ->label('Cancel')
            ->icon($icons['cancel'] ?? 'heroicon-o-x-mark')
            ->color('gray');
    }

    protected function afterCreate(): void
    {
        try {
            $this->record->syncWithMailerLite();
            
            Notification::make()
                ->title('Subscriber Created Successfully!')
                ->body("Added {$this->record->email} to your MailerLite account and local database.")
                ->success()
                ->duration(5000)
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Partial Success')
                ->body('Subscriber was created locally but could not be synced with MailerLite: ' . $e->getMessage())
                ->warning()
                ->duration(8000)
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
