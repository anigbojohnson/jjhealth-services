<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_register' => env('GOOGLE_REDIRECT_REGISTER_URI'),
        'redirect_login' => env('GOOGLE_REDIRECT_LOGIn_URI'),
    ],
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect_register' => env('MICROSOFT_REDIRECT_REGISTER_URI'),
        'redirect_login' => env('MICROSOFT_REDIRECT_LOGIn_URI'),

    ],
    'google_drive' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => '/auth/google-drive/callback',
        'api_key' => env('GOOGLE_DRIVE_API_KEY')
    ],

    'dropbox' => [
        'client_id' => env('DROPBOX_CLIENT_ID'),
        'client_secret' => env('DROPBOX_CLIENT_SECRET'),
        'redirect' => 'http://localhost:8000/auth/dropbox/callback'
    ],
    'stripe' => [
        'key'=>env('STRIPE_TEST_PK'),
        'secret'=> env('STRIPE_TEST_SK'),
],

];
