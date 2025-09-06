<?php

namespace Ihasan\FilamentMailerLite\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class ViewMembersAction extends Action
{
    public static function getDefaultName(): string
    {
        return 'view_members';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('View Members')
            ->icon('heroicon-o-users')
            ->color('info')
            ->action(function () {
                try {
                    $group = $this->getRecord();
                    
                    if (!$group->mailerlite_id) {
                        Notification::make()
                            ->title('Cannot view members')
                            ->body('This group has not been synced with MailerLite yet. Please sync the group first.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $response = MailerLite::groups()->getSubscribers($group->mailerlite_id);
                    $subscribers = $response['data'] ?? [];
                    $totalMembers = count($subscribers);

                    Notification::make()
                        ->title('Group Members')
                        ->body("Group '{$group->name}' has {$totalMembers} member(s).")
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Failed to get members')
                        ->body('Failed to fetch group members: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
