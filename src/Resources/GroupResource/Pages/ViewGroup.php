<?php

namespace Ihasan\FilamentMailerLite\Resources\GroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Ihasan\FilamentMailerLite\Resources\GroupResource;
use Ihasan\FilamentMailerLite\Models\Subscriber;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Illuminate\Support\Facades\Log;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_members')
                ->label('View Members')
                ->icon('heroicon-o-users')
                ->color('info')
                ->action(function () {
                    try {
                        $group = $this->record;
                        
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
                }),
            Actions\Action::make('import_subscribers')
                ->label('Add Subscribers to Group')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('subscriber_ids')
                        ->label('Select Subscribers')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            try {
                                $group = $this->record;
                                
                                if (!$group->mailerlite_id) {
                                    return [];
                                }

                                $response = MailerLite::subscribers()->list();
                                $subscribers = $response['data'] ?? [];

                                $options = [];
                                $options = collect($subscribers)
                                    ->reject(fn($subscriber) => empty($subscriber['id']) && empty($subscriber['email']))
                                    ->mapWithKeys(function ($subscriber) {
                                        $name = $subscriber['name'] ?? $subscriber['email'];
                                        return [$subscriber['email'] => "{$name} ({$subscriber['email']})"];
                                    })
                                    ->toArray();

                                return $options;
                            } catch (\Throwable $e) {
                                return [];
                            }
                        })
                        ->required()
                        ->helperText('Search and select subscribers to add to this group'),
                ])
                ->action(function (array $data) {
                    try {
                        $group = $this->record;
                        
                        if (!$group->mailerlite_id) {
                            Notification::make()
                                ->title('Cannot add subscribers')
                                ->body('This group has not been synced with MailerLite yet. Please sync the group first.')
                                ->warning()
                                ->send();
                            return;
                        }


                        $subscriberIds = $data['subscriber_ids'] ?? [];
                        
                        if (empty($subscriberIds)) {
                            Notification::make()
                                ->title('No subscribers selected')
                                ->body('Please select at least one subscriber to add to the group.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $addedCount = collect($subscriberIds)->reduce(function ($count, $subscriberEmail) use ($group) {
                            try {
                                // Use the group service to add subscriber with email data
                                $subscriberData = ['email' => $subscriberEmail];
                                $result = MailerLite::groups()->addSubscribers($group->mailerlite_id, $subscriberData);

                                if ($result) {
                                    // Try to get subscriber details for local storage
                                    try {
                                        $subscriber = MailerLite::subscribers()->email($subscriberEmail)->find();
                                        if ($subscriber) {
                                            Subscriber::updateOrCreate(
                                                ['mailerlite_id' => $subscriber['id']],
                                                [
                                                    'email' => $subscriber['email'],
                                                    'name' => $subscriber['name'] ?? null,
                                                    'status' => $subscriber['status'] ?? 'active',
                                                    'subscribed_at' => $subscriber['subscribed_at'] ?? now(),
                                                    'meta' => $subscriber['fields'] ?? [],
                                                ]
                                            );
                                        }
                                    } catch (\Throwable $e) {
                                        // If we can't get subscriber details, continue anyway
                                        Log::warning('Could not fetch subscriber details for local storage', [
                                            'subscriber_email' => $subscriberEmail,
                                            'error' => $e->getMessage()
                                        ]);
                                    }

                                    return $count + 1;
                                }

                                return $count;
                            } catch (\Throwable $e) {
                                // Log the error for debugging but continue with other subscribers
                                Log::error('Failed to add subscriber to group', [
                                    'subscriber_email' => $subscriberEmail,
                                    'group_id' => $group->mailerlite_id,
                                    'error' => $e->getMessage()
                                ]);
                                return $count;
                            }
                        }, 0);

                        if ($addedCount > 0) {
                            Notification::make()
                                ->title('Subscribers added successfully')
                                ->body("Added {$addedCount} subscriber(s) to group '{$group->name}'.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to add subscribers')
                                ->body('No subscribers could be added to the group.')
                                ->warning()
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Add subscribers failed')
                            ->body('Failed to add subscribers: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('sync_group')
                ->label('Sync Group')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    try {
                        $this->record->syncWithMailerLite();
                        Notification::make()
                            ->title('Group synced successfully')
                            ->body("Group '{$this->record->name}' has been synced with MailerLite.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body('Failed to sync group: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
