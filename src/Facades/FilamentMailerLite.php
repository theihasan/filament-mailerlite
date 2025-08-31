<?php

namespace Ihasan\FilamentMailerLite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ihasan\FilamentMailerLite\FilamentMailerLite
 */
class FilamentMailerLite extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ihasan\FilamentMailerLite\FilamentMailerLite::class;
    }
}
