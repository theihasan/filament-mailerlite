<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;

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
}


