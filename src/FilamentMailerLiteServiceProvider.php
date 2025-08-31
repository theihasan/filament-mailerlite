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
            ->hasConfigFile('filament-mailerlite');
    }
}
