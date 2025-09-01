<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\FilamentMailerLite\DTOs\SubscriberData;
use Ihasan\FilamentMailerLite\Enums\SubscriberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
            if ($this->mailerlite_id) {
                $builder = MailerLite::subscribers()->email($this->email);
                
                if (!empty($this->name)) {
                    $builder->named($this->name);
                }
                
                if (!empty($this->fields)) {
                    $builder->withFields($this->fields);
                }
                
                return $builder->update($this->mailerlite_id);
            } else {
                $builder = MailerLite::subscribers()->email($this->email);
                
                if (!empty($this->name)) {
                    $builder->named($this->name);
                }
                
                if (!empty($this->fields)) {
                    $builder->withFields($this->fields);
                }
                
                $result = $builder->subscribe();
                
                if (isset($result['id'])) {
                    $this->update(['mailerlite_id' => $result['id']]);
                }
                
                return $result;
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to sync with MailerLite: ' . $e->getMessage());
        }
    }

    /**
     * Create subscriber from MailerLite data
     */
    public static function createFromMailerLite(array $data): self
    {
        $dto = SubscriberData::fromMailerLiteArray($data);
        return self::create($dto->toArray());
    }

    /**
     * Create subscriber from DTO
     */
    public static function createFromDto(SubscriberData $dto): self
    {
        return self::create($dto->toArray());
    }

    /**
     * Scope for active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('status', SubscriberStatus::ACTIVE)->whereNull('unsubscribed_at');
    }

    /**
     * Scope for unsubscribed subscribers
     */
    public function scopeUnsubscribed($query)
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
