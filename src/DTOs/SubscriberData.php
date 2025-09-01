<?php

namespace Ihasan\FilamentMailerLite\DTOs;

use Carbon\Carbon;

class SubscriberData
{
    public function __construct(
        public readonly ?string $mailerlite_id,
        public readonly string $email,
        public readonly ?string $name,
        public readonly int $sent = 0,
        public readonly int $opens_count = 0,
        public readonly int $clicks_count = 0,
        public readonly float $open_rate = 0.0,
        public readonly float $click_rate = 0.0,
        public readonly ?string $ip_address = null,
        public readonly ?Carbon $subscribed_at = null,
        public readonly ?Carbon $unsubscribed_at = null,
        public readonly array $fields = [],
        public readonly array $groups = [],
        public readonly array $location = [],
        public readonly string $status = 'active',
        public readonly ?string $source = null,
        public readonly ?Carbon $opted_in_at = null,
        public readonly ?string $optin_ip = null,
    ) {}

    public static function fromMailerLiteArray(array $data): self
    {
        return new self(
            mailerlite_id: $data['id'] ?? null,
            email: $data['email'],
            name: $data['name'] ?? null,
            sent: $data['sent'] ?? 0,
            opens_count: $data['opens_count'] ?? 0,
            clicks_count: $data['clicks_count'] ?? 0,
            open_rate: $data['open_rate'] ?? 0.0,
            click_rate: $data['click_rate'] ?? 0.0,
            ip_address: $data['ip_address'] ?? null,
            subscribed_at: isset($data['subscribed_at']) ? Carbon::parse($data['subscribed_at']) : now(),
            unsubscribed_at: isset($data['unsubscribed_at']) ? Carbon::parse($data['unsubscribed_at']) : null,
            fields: $data['fields'] ?? [],
            groups: $data['groups'] ?? [],
            location: $data['location'] ?? [],
            status: $data['status'] ?? 'active',
            source: $data['source'] ?? null,
            opted_in_at: isset($data['opted_in_at']) ? Carbon::parse($data['opted_in_at']) : null,
            optin_ip: $data['optin_ip'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'mailerlite_id' => $this->mailerlite_id,
            'email' => $this->email,
            'name' => $this->name,
            'sent' => $this->sent,
            'opens_count' => $this->opens_count,
            'clicks_count' => $this->clicks_count,
            'open_rate' => $this->open_rate,
            'click_rate' => $this->click_rate,
            'ip_address' => $this->ip_address,
            'subscribed_at' => $this->subscribed_at,
            'unsubscribed_at' => $this->unsubscribed_at,
            'fields' => $this->fields,
            'groups' => $this->groups,
            'location' => $this->location,
            'status' => $this->status,
            'source' => $this->source,
            'opted_in_at' => $this->opted_in_at,
            'optin_ip' => $this->optin_ip,
        ];
    }
}
