<?php

namespace Ihasan\FilamentMailerLite\Resources\CampaignResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Ihasan\FilamentMailerLite\Resources\CampaignResource;
use Filament\Notifications\Notification;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        $icons = config('filament-mailerlite.icons.actions');
        return [
            Actions\CreateAction::make()->icon($icons['create'] ?? 'heroicon-o-plus-circle'),
        ];
    }
}


