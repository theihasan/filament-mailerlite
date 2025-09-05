<?php

namespace Ihasan\FilamentMailerLite\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class SubscribersTable extends Entry
{
    protected string $view = 'filament-mailerlite::infolists.components.subscribers-table';

    public static function make(string $name): static
    {
        return parent::make($name);
    }

    public function getSubscribers(): array
    {
        $record = $this->getRecord();
        
        if (!$record->mailerlite_id) {
            return [];
        }

        try {
            $response = MailerLite::groups()->getSubscribers($record->mailerlite_id);
            $subscribers = $response['data'] ?? [];

            return collect($subscribers)->map(function ($subscriber) {
                // Extract name from fields or use email as fallback
                $name = $subscriber['fields']['name'] ?? 
                       $subscriber['fields']['first_name'] ?? 
                       $subscriber['fields']['last_name'] ?? 
                       null;
                
                // If name is still null or empty, use email prefix
                if ($name === null || $name === '') {
                    $email = $subscriber['email'] ?? '';
                    $name = $email ? explode('@', $email)[0] : 'N/A';
                }
                
                return [
                    'name' => $name,
                    'email' => $subscriber['email'] ?? 'N/A',
                    'subscribed_at' => $subscriber['subscribed_at'] ?? $subscriber['created_at'] ?? null,
                ];
            })->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
