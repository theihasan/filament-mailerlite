<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MailerLite API Key
    |--------------------------------------------------------------------------
    |
    | Your MailerLite API key. You can find this in your MailerLite account
    | under Account > Integrations > API.
    |
    */
    'key' => env('MAILERLITE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | MailerLite API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the MailerLite API. You should not need to change this.
    |
    */
    'url' => env('MAILERLITE_API_URL', 'https://connect.mailerlite.com/api/'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout for API requests in seconds.
    |
    */
    'timeout' => env('MAILERLITE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Database Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the table used to store MailerLite subscribers locally.
    |
    */
    'subscribers_table' => env('MAILERLITE_SUBSCRIBERS_TABLE', 'mailerlite_subscribers'),

    /*
    |--------------------------------------------------------------------------
    | UI Icons
    |--------------------------------------------------------------------------
    |
    | Configure Heroicons used throughout the
    | Filament resources and pages. You can override any of these in your app's
    | config by publishing this file and changing the classes.
    |
    */
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
            'cancel' => 'heroicon-o-x-mark',
            'send_test_email' => 'heroicon-o-paper-airplane',
            'send' => 'heroicon-o-paper-airplane',
            'schedule' => 'heroicon-o-calendar-days',
            'duplicate' => 'heroicon-o-square-2-stack',
            'cancel_campaign' => 'heroicon-o-no-symbol',
            'add_to_group' => 'heroicon-o-user-plus',
            'remove_from_group' => 'heroicon-o-user-minus',
        ],
    ],
];
