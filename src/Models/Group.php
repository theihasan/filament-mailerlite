<?php

namespace Ihasan\FilamentMailerLite\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
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
}


