<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class Group extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'mailerlite_id',
        'name',
        'description',
        'total',
        'type',
        'meta',
    ];

    public function casts(): array
    {
        return [
            'total' => 'integer',
            'meta' => 'array',
        ];
    }

    public function getTable()
    {
        return 'mailerlite_groups';
    }

    /**
     * Sync group with MailerLite
     */
    public function syncWithMailerLite(): array
    {
        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
            ];

            if ($this->mailerlite_id) {
                $result = MailerLite::groups()->update($this->mailerlite_id, $data);
            } else {
                $result = MailerLite::groups()->create($data);
                
                if (isset($result['id'])) {
                    $this->update(['mailerlite_id' => $result['id']]);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Failed to sync with MailerLite: ' . $e->getMessage());
        }
    }
}


