<?php

namespace Ihasan\FilamentMailerLite;

use Ihasan\FilamentMailerLite\Commands\FilamentMailerLiteCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMailerLiteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-mailerlite')
            ->hasConfigFile('filament-mailerlite')
            ->hasViews('filament-mailerlite')
            ->hasMigrations([
                'create_mailerlite_subscribers_table',
                'create_mailerlite_campaigns_table',
                'create_mailerlite_groups_table',
                'create_mailerlite_segments_table',
            ]);
    }
}
