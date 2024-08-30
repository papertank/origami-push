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

    'fcm' => [
        'project_id' => env('PUSH_FCM_PROJECT'),
        /** Options: android, apns, webpush */
        'platforms' => ['android', 'apns'],
        'options' => [
            'android' => [
                /**
                 * If enabled, setBadge will set the payload's android.notification.notification_count to 1.
                 * This is because the Android notification dot auto-increments the badge by the given number.
                 * See: https://developer.android.com/develop/ui/views/notifications/badges
                 */
                'notification_count_increments' => true,
            ],
        ],
    ],

];
