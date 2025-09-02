<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class Segment extends Model
{
    protected $fillable = [
        'mailerlite_id',
        'name',
        'rules',
        'total',
        'meta',
    ];

    public function casts(): array
    {
        return [
            'rules' => 'array',
            'total' => 'integer',
            'meta' => 'array',
        ];
    }

    public function getTable()
    {
        return 'mailerlite_segments';
    }

    /**
     * Sync segment with MailerLite
     */
    public function syncWithMailerLite(): array
    {
        try {
            $data = [
                'name' => $this->name,
                'rules' => $this->rules ?? [],
            ];

            if ($this->mailerlite_id) {
                $result = MailerLite::segments()->update($this->mailerlite_id, $data);
            } else {
                $result = MailerLite::segments()->create($data);
                
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


