# Upgrading

## From v4 to v5

v5 is a breaking change upgrade to the Firebase Cloud Messaging (Fcm) driver. This now uses the HTTP v1 API and service account / oAuth, with the old depreciated legacy API shutting down from July 2024 - https://firebase.google.com/docs/cloud-messaging/migrate-v1.

The config has changed:

Before:

```php
    'fcm' => [
        'key' => env('PUSH_FCM_KEY', ''),
        'type' => \Origami\Push\Drivers\Fcm::NOTIFICATION,
    ],
```

After:

```php
    'fcm' => [
        'project_id' => env('PUSH_FCM_PROJECT'),
        /** Options: android, apns, webpush */
        'platforms' => ['android', 'apns'],
    ],
```

The `PUSH_FCM_PROJECT` is required for the Api request and can be found on the Firebase console.

Authentication also now uses a service account JSON file to generate an oAuth access token (behind the scenes using the https://github.com/papertank/origami-google-auth package). You should 

1. In the Firebase console, open Settings > Service Accounts.
2. Click Generate New Private Key, then confirm by clicking Generate Key.
3. Securely store the JSON file containing the key at the following path `storage/app/google-auth/service-account-credentials.json`

If you want to change the loaded path you can set the `GOOGLE_AUTH_SERVICE_CREDENTIALS` environment variable, or to change other settings you can publish the google-auth config: `php artisan vendor:publish --provider="Origami\GoogleAuth\GoogleAuthServiceProvider"`


## From v3 to v4

v4 makes some breaking changes to contracts, config and PushNotification methods. You should make the following changes:

- The `message` property has been changed to `body` and various other properties and methods have changed inside the `Origami\Push\PushNotification` class. 

Before:

```php
$notification =  (new PushNotification)
    ->message('Friendly reminder: Your availability looks like it may need updating. Please ensure you keep your availability regularly updated.')
    ->badge(1)
    ->meta([
        'event' => 'AvailabilityReminder',
    ]);
```

After:

```php
$notification =  (new PushNotification)
    ->setBody('Friendly reminder: Your availability looks like it may need updating. Please ensure you keep your availability regularly updated.')
    ->setBadge(1)
    ->setMeta([
        'event' => 'AvailabilityReminder',
    ]);
```

For APNs, the meta attributes are now included outside of the apns key / namespace, and sit at the top-level.