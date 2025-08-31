<?php

namespace Ihasan\FilamentMailerLite\Commands;

use Illuminate\Console\Command;

class FilamentMailerLiteCommand extends Command
{
    public $signature = 'filament-mailerlite';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
