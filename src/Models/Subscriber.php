<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\FilamentMailerLite\DTOs\SubscriberData;
use Ihasan\FilamentMailerLite\Enums\SubscriberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ihasan\FilamentMailerLite\Pipelines\SubscriberPipeline;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailerlite_id',
        'email',
        'name',
        'sent',
        'opens_count',
        'clicks_count',
        'open_rate',
        'click_rate',
        'ip_address',
        'subscribed_at',
        'unsubscribed_at',
        'fields',
        'groups',
        'location',
        'status',
        'source',
        'opted_in_at',
        'optin_ip',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public function casts(): array
    {
        return [
            'fields' => 'array',
            'groups' => 'array',
            'location' => 'array',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'opted_in_at' => 'datetime',
            'sent' => 'integer',
            'opens_count' => 'integer',
            'clicks_count' => 'integer',
            'open_rate' => 'decimal:2',
            'click_rate' => 'decimal:2',
        ];
    }

    protected $dates = [
        'subscribed_at',
        'unsubscribed_at',
        'opted_in_at',
        'deleted_at',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('filament-mailerlite.subscribers_table', 'mailerlite_subscribers');
    }

    /**
     * Sync subscriber with MailerLite
     */
    public function syncWithMailerLite(): array
    {
        try {
            $data = [
                'email' => $this->email,
                'name' => $this->name,
                'fields' => $this->fields ?? [],
            ];

            // If we have a mailerlite_id, try to update
            if ($this->mailerlite_id) {
                return SubscriberPipeline::create($data)->update($this->mailerlite_id);
            } else {
                // Check if subscriber exists in MailerLite by email
                $existing = MailerLite::subscribers()->email($this->email)->find();
                
                if ($existing && isset($existing['id'])) {
                    $result = SubscriberPipeline::create($data)->update($existing['id']);
                    $this->update(['mailerlite_id' => $existing['id']]);
                    return $result;
                } else {
                    $result = SubscriberPipeline::create($data)->process();
                    
                    if (isset($result['id'])) {
                        $this->update(['mailerlite_id' => $result['id']]);
                    }
                    
                    return $result;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to sync with MailerLite: ' . $e->getMessage());
        }
    }

    /**
     * Create subscriber from MailerLite data
     */
    public static function createFromMailerLite(array $data): static
    {
        $dto = SubscriberData::fromMailerLiteArray($data);
        return static::create($dto->toArray());
    }

    /**
     * Create subscriber from DTO
     */
    public static function createFromDto(SubscriberData $dto): static
    {
        return self::create($dto->toArray());
    }

    /**
     * Scope for active subscribers
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', SubscriberStatus::ACTIVE)->whereNull('unsubscribed_at');
    }

    /**
     * Scope for unsubscribed subscribers
     */
    public function scopeUnsubscribed($query): Builder
    {
        return $query->where('status', SubscriberStatus::UNSUBSCRIBED)->orWhereNotNull('unsubscribed_at');
    }

    /**
     * Get formatted location
     */
    public function getFormattedLocationAttribute(): string
    {
        if (!$this->location) {
            return 'Unknown';
        }

        $parts = [];
        if (isset($this->location['city'])) $parts[] = $this->location['city'];
        if (isset($this->location['region'])) $parts[] = $this->location['region'];
        if (isset($this->location['country'])) $parts[] = $this->location['country'];

        return implode(', ', $parts) ?: 'Unknown';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            SubscriberStatus::ACTIVE => 'success',
            SubscriberStatus::UNSUBSCRIBED => 'danger',
            SubscriberStatus::BOUNCED => 'warning',
            SubscriberStatus::JUNK => 'gray',
            default => 'gray',
        };
    }
}
