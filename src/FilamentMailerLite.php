<?php

namespace Ihasan\FilamentMailerLite;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Ihasan\FilamentMailerLite\Pages\MailerLiteDashboard;
use Ihasan\LaravelMailerlite\Facades\MailerLite;

class FilamentMailerLite implements Plugin
{
    use EvaluatesClosures;

    protected string $navigationGroup = 'MailerLite';
    protected array $navigationItems = [];
    protected bool $hasDefaultNavigationItems = true;

    public function getId(): string
    {
        return 'filament-mailerlite';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            MailerLiteDashboard::class,
        ]);

        $navigationItems = $this->navigationItems;
        
        if ($this->hasDefaultNavigationItems) {
            $defaultItems = [
                NavigationItem::make('MailerLite Dashboard')
                    ->url(fn() => MailerLiteDashboard::getUrl())
                    ->icon('heroicon-o-envelope')
                    ->group($this->getNavigationGroup())
                    ->sort(1),
                
                NavigationItem::make('Subscribers')
                    ->url('#')
                    ->icon('heroicon-o-users')
                    ->group($this->getNavigationGroup())
                    ->sort(2),
                    
                NavigationItem::make('Campaigns')
                    ->url('#')
                    ->icon('heroicon-o-megaphone')
                    ->group($this->getNavigationGroup())
                    ->sort(3),
                    
                NavigationItem::make('Settings')
                    ->url('#')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->group($this->getNavigationGroup())
                    ->sort(4),
            ];
            
            $navigationItems = array_merge($defaultItems, $navigationItems);
        }
        
        if (!empty($navigationItems)) {
            $panel->navigationItems($navigationItems);
        }
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

    public function navigationItems(array $items): static
    {
        $this->navigationItems = $items;

        return $this;
    }

    public function disableDefaultNavigationItems(): static
    {
        $this->hasDefaultNavigationItems = false;

        return $this;
    }

    public function enableDefaultNavigationItems(): static
    {
        $this->hasDefaultNavigationItems = true;

        return $this;
    }

}
