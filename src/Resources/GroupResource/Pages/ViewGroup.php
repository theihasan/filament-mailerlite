<?php

namespace Ihasan\FilamentMailerLite\Resources\GroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Ihasan\FilamentMailerLite\Resources\GroupResource;
use Ihasan\FilamentMailerLite\Actions\ViewMembersAction;
use Ihasan\FilamentMailerLite\Actions\ImportSubscribersAction;
use Ihasan\FilamentMailerLite\Actions\SyncGroupAction;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewMembersAction::make(),
            ImportSubscribersAction::make(),
            SyncGroupAction::make(),
        ];
    }
}
