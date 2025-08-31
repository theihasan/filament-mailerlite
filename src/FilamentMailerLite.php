<?php

namespace Ihasan\FilamentMailerLite;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentMailerLite implements Plugin
{
    use EvaluatesClosures;

    protected string $navigationGroup = 'MailerLite';

    public function getId(): string
    {
        return 'filament-mailerlite';
    }

    public function register(Panel $panel): void
    {
        // Register any resources, pages, or widgets here
    }
    
    public function boot(Panel $panel): void
    {
        // Boot the plugin - register any additional functionality
    }

    public static function make(): static
    {
        return new static();
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function navigationGroup(string|callable $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->evaluate($this->navigationGroup) ?? 'MailerLite';
    }

}
