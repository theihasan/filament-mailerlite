<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'mailerlite_id',
        'name',
        'subject',
        'status',
        'type',
        'from_name',
        'from_email',
        'send_at',
        'groups',
        'segments',
        'settings',
        'stats',
    ];

    public function casts(): array
    {
        return [
            'send_at' => 'datetime',
            'groups' => 'array',
            'segments' => 'array',
            'settings' => 'array',
            'stats' => 'array',
        ];
    }

    /**
     * Get the table name for campaigns.
     */
    public function getTable()
    {
        return 'mailerlite_campaigns';
    }
}


