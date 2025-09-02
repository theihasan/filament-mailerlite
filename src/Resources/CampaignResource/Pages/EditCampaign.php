<?php

namespace Ihasan\FilamentMailerLite\Resources\CampaignResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Ihasan\FilamentMailerLite\Resources\CampaignResource;
use Filament\Notifications\Notification;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        $icons = config('filament-mailerlite.icons.actions');
        return [
            Actions\ViewAction::make()->icon($icons['view'] ?? 'heroicon-o-eye'),
            Actions\DeleteAction::make()->icon($icons['delete'] ?? 'heroicon-o-trash'),
        ];
    }
}


