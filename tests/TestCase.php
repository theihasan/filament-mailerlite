<?php

namespace Ihasan\FilamentMailerLite\Tests;

use Ihasan\FilamentMailerLite\FilamentMailerLiteServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ihasan\\FilamentMailerLite\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentMailerLiteServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        // Ensure MailerLite API key is available for vendor package during tests
        config()->set('mailerlite.key', 'test-api-key');
        putenv('MAILERLITE_API_KEY=test-api-key');
        $_ENV['MAILERLITE_API_KEY'] = 'test-api-key';
        $_SERVER['MAILERLITE_API_KEY'] = 'test-api-key';
        $table = config('filament-mailerlite.subscribers_table', 'mailerlite_subscribers');

        Schema::dropIfExists($table);
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('mailerlite_id')->nullable()->index();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('fields')->nullable();
            $table->json('groups')->nullable();
            $table->timestamp('opted_in_at')->nullable();
            $table->string('optin_ip')->nullable();
            $table->string('source')->nullable();
            $table->unsignedInteger('sent')->default(0);
            $table->unsignedInteger('opens_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->decimal('open_rate', 5, 2)->nullable();
            $table->decimal('click_rate', 5, 2)->nullable();
            $table->string('ip_address')->nullable();
            $table->json('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
