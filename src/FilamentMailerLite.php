<?php

namespace Ihasan\FilamentMailerLite;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Support\Concerns\EvaluatesClosures;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\FilamentMailerLite\Pages\MailerLiteDashboard;
use Ihasan\FilamentMailerLite\Resources\SubscriberResource;
use Ihasan\FilamentMailerLite\Resources\CampaignResource;
use Ihasan\FilamentMailerLite\Resources\GroupResource;
use Ihasan\FilamentMailerLite\Resources\SegmentResource;

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
        $panel
            ->pages([
                MailerLiteDashboard::class,
            ])
            ->resources([
                SubscriberResource::class,
                CampaignResource::class,
                GroupResource::class,
                SegmentResource::class,
            ]);

        

        $navigationItems = $this->navigationItems;
        
        if ($this->hasDefaultNavigationItems) {
            $defaultItems = [
                NavigationItem::make('Dashboard')
                    ->url(fn() => MailerLiteDashboard::getUrl())
                    ->icon('heroicon-o-envelope')
                    ->group($this->getNavigationGroup())
                    ->sort(1),
                
                NavigationItem::make('Subscribers')
                    ->url(function(){
                        return SubscriberResource::getUrl();
                    })
                    ->icon(config('filament-mailerlite.icons.navigation', 'heroicon-o-users'))
                    ->group($this->getNavigationGroup())
                    ->sort(2),
                    
                NavigationItem::make('Campaigns')
                    ->url(function(){
                        return CampaignResource::getUrl();
                    })
                    ->icon(config('filament-mailerlite.icons.campaign_navigation', 'heroicon-o-megaphone'))
                    ->group($this->getNavigationGroup())
                    ->sort(3),
                    
                NavigationItem::make('Settings')
                    ->url('#')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->group($this->getNavigationGroup())
                    ->sort(4),
                NavigationItem::make('Groups')
                    ->url(function(){ return GroupResource::getUrl(); })
                    ->icon(config('filament-mailerlite.icons.groups_navigation', 'heroicon-o-rectangle-group'))
                    ->group($this->getNavigationGroup())
                    ->sort(5),
                NavigationItem::make('Segments')
                    ->url(function(){ return SegmentResource::getUrl(); })
                    ->icon(config('filament-mailerlite.icons.segments_navigation', 'heroicon-o-squares-2x2'))
                    ->group($this->getNavigationGroup())
                    ->sort(6),
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
