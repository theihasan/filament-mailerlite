<?php

namespace Ihasan\FilamentMailerLite\Resources\SubscriberResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Ihasan\FilamentMailerLite\Models\Subscriber;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\LaravelMailerlite\Manager\MailerLiteManager;
use Filament\Notifications\Notification;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importFromMailerLite')
                ->label('Import from MailerLite')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('info')
                ->action(function () {
                    try {
                        $response = MailerLite::subscribers()->all();
                        
                        $subscribers = $response['data'] ?? [];
                        
                        if (empty($subscribers)) {
                            Notification::make()
                                ->title('No Subscribers Found')
                                ->body('No subscribers were found in your MailerLite account or the response format is unexpected. Check logs for details')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $imported = 0;
                        $updated = 0;
                        
                        collect($subscribers)->each(function ($subscriberData) use (&$imported, &$updated) {
                            if (!isset($subscriberData['email']) || empty($subscriberData['email'])) {
                                return;
                            }
                            
                            $existingSubscriber = Subscriber::query()->where('email', $subscriberData['email'])->first();

                            if ($existingSubscriber) {
                                $existingSubscriber->update([
                                    'mailerlite_id' => $subscriberData['id'] ?? $existingSubscriber->mailerlite_id,
                                    'name' => $subscriberData['fields']['name'] ?? $existingSubscriber->name,
                                    'status' => $subscriberData['status'] ?? $existingSubscriber->status,
                                    'source' => $subscriberData['source'] ?? $existingSubscriber->source,
                                    'sent' => $subscriberData['sent'] ?? $existingSubscriber->sent,
                                    'opens_count' => $subscriberData['opens_count'] ?? $existingSubscriber->opens_count,
                                    'clicks_count' => $subscriberData['clicks_count'] ?? $existingSubscriber->clicks_count,
                                    'open_rate' => $subscriberData['open_rate'] ?? $existingSubscriber->open_rate,
                                    'click_rate' => $subscriberData['click_rate'] ?? $existingSubscriber->click_rate,
                                    'ip_address' => $subscriberData['ip_address'] ?? $existingSubscriber->ip_address,
                                    'fields' => $subscriberData['fields'] ?? $existingSubscriber->fields,
                                    'groups' => $subscriberData['groups'] ?? [],
                                    'subscribed_at' => isset($subscriberData['subscribed_at']) 
                                        ? \Carbon\Carbon::parse($subscriberData['subscribed_at']) 
                                        : $existingSubscriber->subscribed_at,
                                    'unsubscribed_at' => isset($subscriberData['unsubscribed_at']) 
                                        ? \Carbon\Carbon::parse($subscriberData['unsubscribed_at']) 
                                        : $existingSubscriber->unsubscribed_at,
                                    'opted_in_at' => isset($subscriberData['opted_in_at']) 
                                        ? \Carbon\Carbon::parse($subscriberData['opted_in_at']) 
                                        : $existingSubscriber->opted_in_at,
                                    'optin_ip' => $subscriberData['optin_ip'] ?? $existingSubscriber->optin_ip,
                                ]);
                                $updated++;
                            } else {
                                Subscriber::createFromMailerLite($subscriberData);
                                $imported++;
                            }
                        });
                        
                        Notification::make()
                            ->title('Import Successful!')
                            ->body("Imported {$imported} new subscribers and updated {$updated} existing ones from MailerLite.")
                            ->success()
                            ->duration(6000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(8000)
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Import Subscribers from MailerLite')
                ->modalDescription('This will import all subscribers from your MailerLite account. Existing subscribers will be updated.')
                ->modalSubmitActionLabel('Import'),
        ];
    }
}
