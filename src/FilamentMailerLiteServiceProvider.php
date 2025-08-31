<?php

namespace Ihasan\FilamentMailerLite;

use Ihasan\FilamentMailerLite\Commands\FilamentMailerLiteCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMailerLiteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-mailerlite')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_mailerlite_table')
            ->hasCommand(FilamentMailerLiteCommand::class);
    }
}
