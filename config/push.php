<?php
return [

    'enabled' => env('PUSH_ENABLED', true),

    'log' => env('PUSH_LOG', false),

    'apns' => [
        'environment' => env('PUSH_APNS_ENV', 'production') == 'production' ? \Origami\Push\Drivers\Apns::PRODUCTION : \Origami\Push\Drivers\Apns::SANDBOX,
        'key_id' => env('PUSH_APNS_KEY_ID'),
        'team_id' => env('PUSH_APNS_TEAM_ID'),
        'app_bundle_id' => env('PUSH_APNS_APP_BUNDLE'),
        'private_key_path' => env('PUSH_APNS_PRIVATE_KEY', storage_path('certificates/apns.p8')),
        'private_key_secret' => env('PUSH_APNS_PRIVATE_KEY_SECRET'),
    ],

    'gcm' => [
        'key' => env('PUSH_GCM_KEY', ''),
    ],

    'fcm' => [
        'key' => env('PUSH_FCM_KEY', ''),
    ],

];
