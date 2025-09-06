<?php

namespace Ihasan\FilamentMailerLite\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\FilamentMailerLite\Pipelines\SubscriberPipeline;

class MailerLiteDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament-mailerlite::pages.dashboard';
    protected static ?string $title = 'MailerLite Dashboard';
    protected static ?string $navigationLabel = 'MailerLite Dashboard';
    protected static ?string $slug = 'mailer-lite-dashboard';
    
    public function getTitle(): string
    {
        return 'MailerLite Dashboard';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-mailerlite.pages.dashboard', false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addSubscriber')
                ->label('Quick Add Subscriber')
                ->icon('heroicon-o-user-plus')
                ->modalWidth('2xl')
                ->form(function (Form $form) {
                    return $form
                        ->schema([
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->label('Email Address')
                                ->placeholder('john@example.com')
                                ->columnSpan(1),
                            TextInput::make('name')
                                ->label('Full Name')
                                ->placeholder('John Doe')
                                ->columnSpan(1),
                            TextInput::make('company')
                                ->label('Company')
                                ->placeholder('Acme Corp')
                                ->columnSpan(1),
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->placeholder('+1 234 567 8900')
                                ->columnSpan(1),
                        ])
                        ->columns(2);
                })
                ->action(function (array $data) {
                    try {
                        $result = SubscriberPipeline::create($data)->process();

                        Notification::make()
                            ->title('Subscriber added successfully!')
                            ->body("Added {$data['email']} to your MailerLite subscribers.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error adding subscriber')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getStats(): array
    {
        try {
           // dd(MailerLite::subscribers()->get());
            $subscriberCount = 0; // MailerLite::subscribers()->count();
            $campaignCount = 0;   // MailerLite::campaigns()->count();
            $groupCount = 0;      // MailerLite::groups()->count();

            return [
                'subscribers' => $subscriberCount,
                'campaigns' => $campaignCount,
                'groups' => $groupCount,
            ];
        } catch (\Exception $e) {
            return [
                'subscribers' => 'N/A',
                'campaigns' => 'N/A',
                'groups' => 'N/A',
            ];
        }
    }
}
