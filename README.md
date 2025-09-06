# Filament MailerLite Integration

Seamlessly integrate MailerLite with your FilamentPHP admin panels using this comprehensive package. Designed for developers building Laravel applications with Filament, this package simplifies email marketing management by connecting your app directly to MailerLite's powerful API.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/theihasan/filament-mailerlite.svg?style=flat-square)](https://packagist.org/packages/theihasan/filament-mailerlite)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/theihasan/filament-mailerlite/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/theihasan/filament-mailerlite/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/theihasan/filament-mailerlite/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/theihasan/filament-mailerlite/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/theihasan/filament-mailerlite.svg?style=flat-square)](https://packagist.org/packages/theihasan/filament-mailerlite)

## Features

- **Complete MailerLite Integration** - Manage subscribers, groups, segments, and campaigns
- **Custom Filament Actions** - Pre-built actions for common MailerLite operations
- **Configurable Navigation** - Show/hide resources based on your needs
- **Real-time Data Sync** - Keep your local data in sync with MailerLite
- **Beautiful UI Components** - Custom infolist components for enhanced data display
- **Comprehensive Error Handling** - User-friendly notifications and error management

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/filament-mailerlite.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/filament-mailerlite)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require theihasan/filament-mailerlite
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-mailerlite-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-mailerlite-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | MailerLite API Configuration
    |--------------------------------------------------------------------------
    */
    'key' => env('MAILERLITE_API_KEY'),
    'url' => env('MAILERLITE_API_URL', 'https://connect.mailerlite.com/api/'),
    'timeout' => env('MAILERLITE_TIMEOUT', 30),
    'subscribers_table' => env('MAILERLITE_SUBSCRIBERS_TABLE', 'mailerlite_subscribers'),

    /*
    |--------------------------------------------------------------------------
    | Navigation Settings
    |--------------------------------------------------------------------------
    */
    'show_navigation' => env('MAILERLITE_SHOW_NAVIGATION', true),
    'resources' => [
        'subscribers' => env('MAILERLITE_SHOW_SUBSCRIBERS_NAVIGATION', true),
        'campaigns' => env('MAILERLITE_SHOW_CAMPAIGNS_NAVIGATION', false),
        'groups' => env('MAILERLITE_SHOW_GROUPS_NAVIGATION', true),
        'segments' => env('MAILERLITE_SHOW_SEGMENTS_NAVIGATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Icons Configuration
    |--------------------------------------------------------------------------
    */
    'icons' => [
        'navigation' => 'heroicon-o-users',
        'campaign_navigation' => 'heroicon-o-megaphone',
        'groups_navigation' => 'heroicon-o-rectangle-group',
        'segments_navigation' => 'heroicon-o-squares-2x2',
        // ... more icon configurations
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-mailerlite-views"
```

## Usage

### Basic Setup

After installation, the package will automatically register the MailerLite resources in your Filament panel. You can access them through the navigation menu.

### Custom Actions

The package includes several custom actions for enhanced functionality:

#### Group Management Actions

The Group view page includes three powerful custom actions:

1. **View Members Action** - Shows total member count in a notification
2. **Import Subscribers Action** - Allows you to add existing subscribers to a group
3. **Sync Group Action** - Syncs the group with MailerLite

```php
// These actions are automatically available in the Group view page
// No additional setup required
```

#### Using Custom Actions in Your Own Resources

You can also use these actions in your own resources:

```php
use Ihasan\FilamentMailerLite\Actions\ViewMembersAction;
use Ihasan\FilamentMailerLite\Actions\ImportSubscribersAction;
use Ihasan\FilamentMailerLite\Actions\SyncGroupAction;

// In your resource's getHeaderActions() method
protected function getHeaderActions(): array
{
    return [
        ViewMembersAction::make(),
        ImportSubscribersAction::make(),
        SyncGroupAction::make(),
    ];
}
```

### Navigation Configuration

You can control which resources appear in the navigation menu through the config file:

#### Environment Variables

Add to your `.env` file:

```env
# Show/hide entire MailerLite navigation
MAILERLITE_SHOW_NAVIGATION=true

# Control individual resources
MAILERLITE_SHOW_SUBSCRIBERS_NAVIGATION=true
MAILERLITE_SHOW_CAMPAIGNS_NAVIGATION=false
MAILERLITE_SHOW_GROUPS_NAVIGATION=true
MAILERLITE_SHOW_SEGMENTS_NAVIGATION=true
```

#### Config File

Edit `config/filament-mailerlite.php`:

```php
'resources' => [
    'subscribers' => true,   // Show in navigation
    'campaigns' => false,    // Hide from navigation
    'groups' => true,        // Show in navigation
    'segments' => true,      // Show in navigation
],
```

### Custom Components

The package includes custom infolist components for enhanced data display:

- **SubscribersTable** - Beautiful table component for displaying group subscribers
- **Copy Email Functionality** - One-click email copying with notifications
- **Real-time Data** - Live subscriber counts and data synchronization

### API Integration

The package provides seamless integration with MailerLite's API:

```php
use Ihasan\LaravelMailerlite\Facades\MailerLite;

// Get subscribers
$subscribers = MailerLite::subscribers()->list();

// Get groups
$groups = MailerLite::groups()->list();

// Add subscriber to group
MailerLite::groups()->addSubscriber($groupId, $subscriberData);
```

## Advanced Features

### Custom Infolist Components

The package includes a custom `SubscribersTable` component that provides:

- **Beautiful Styling** - Modern, responsive table design
- **Copy Email Functionality** - One-click email copying with toast notifications
- **Real-time Data** - Live subscriber counts and information
- **Error Handling** - Graceful handling of API errors
- **Responsive Design** - Works perfectly on all screen sizes

### Group Management

Enhanced group management with:

- **Real-time Subscriber Count** - Shows live count from MailerLite
- **Bulk Subscriber Import** - Add multiple subscribers to groups at once
- **Sync Functionality** - Keep local data in sync with MailerLite
- **Member Viewing** - Quick access to group member information

### Error Handling & Notifications

Comprehensive error handling with:

- **User-friendly Notifications** - Clear success, warning, and error messages
- **API Error Handling** - Graceful handling of MailerLite API errors
- **Logging** - Detailed logging for debugging and monitoring
- **Validation** - Proper validation for all operations

## Configuration Options

### Complete Configuration Reference

```php
return [
    // API Configuration
    'key' => env('MAILERLITE_API_KEY'),
    'url' => env('MAILERLITE_API_URL', 'https://connect.mailerlite.com/api/'),
    'timeout' => env('MAILERLITE_TIMEOUT', 30),
    'subscribers_table' => env('MAILERLITE_SUBSCRIBERS_TABLE', 'mailerlite_subscribers'),

    // Navigation Control
    'show_navigation' => env('MAILERLITE_SHOW_NAVIGATION', true),
    'resources' => [
        'subscribers' => env('MAILERLITE_SHOW_SUBSCRIBERS_NAVIGATION', true),
        'campaigns' => env('MAILERLITE_SHOW_CAMPAIGNS_NAVIGATION', false),
        'groups' => env('MAILERLITE_SHOW_GROUPS_NAVIGATION', true),
        'segments' => env('MAILERLITE_SHOW_SEGMENTS_NAVIGATION', true),
    ],

    // UI Customization
    'icons' => [
        'navigation' => 'heroicon-o-users',
        'campaign_navigation' => 'heroicon-o-megaphone',
        'groups_navigation' => 'heroicon-o-rectangle-group',
        'segments_navigation' => 'heroicon-o-squares-2x2',
        'actions' => [
            'create' => 'heroicon-o-plus-circle',
            'import' => 'heroicon-o-cloud-arrow-down',
            'sync' => 'heroicon-o-arrow-path',
            'view' => 'heroicon-o-eye',
            'edit' => 'heroicon-o-pencil-square',
            'delete' => 'heroicon-o-trash',
            // ... more action icons
        ],
    ],
];
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Abul Hassan](https://github.com/theihasan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
