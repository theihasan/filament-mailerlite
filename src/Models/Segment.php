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
            if ($this->mailerlite_id) {
                // Update existing segment
                $builder = MailerLite::segments()->name($this->name);
                
                // Add rules as filters
                if (!empty($this->rules)) {
                    foreach ($this->rules as $rule) {
                        if (isset($rule['field'], $rule['operator'], $rule['value'])) {
                            $builder->whereField($rule['field'], $rule['operator'], $rule['value']);
                        }
                    }
                }
                
                $result = $builder->update($this->mailerlite_id);
            } else {
                // Create new segment
                $builder = MailerLite::segments()->name($this->name);
                
                // Add rules as filters
                if (!empty($this->rules)) {
                    foreach ($this->rules as $rule) {
                        if (isset($rule['field'], $rule['operator'], $rule['value'])) {
                            $builder->whereField($rule['field'], $rule['operator'], $rule['value']);
                        }
                    }
                } else {
                    // Add a default filter if no rules are provided
                    $builder->whereField('email', 'is_not_empty', '');
                }
                
                $result = $builder->create();
                
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


