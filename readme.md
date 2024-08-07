# Origami Push - Laravel Push Notifications

This package adds a push notification channel for notifications in Laravel 6 or greater projects.

For more on notification channels, visit [https://laravel.com/docs/8.x/notifications](https://laravel.com/docs/8.x/notifications)

## Installation

Install this package through Composer.

```
composer require origami/push
```

## Requirements

This package is designed to work with Laravel >= 6.

## Configuration

There are some API configuration options that you’ll want to overwrite. First, publish the default configuration.

```bash
php artisan vendor:publish --provider="Origami\Api\ApiServiceProvider"
```

This will add a new configuration file to: `config/push.php`.

### APNs

Version 4+ of this package uses [edamov/pushok](https://github.com/edamov/pushok) for the APNs transport and logic. You should add the following to your .env file to setup the required config, or see the edamov/pushok readme for Certificate (.pem) options.

The default private key location is `storage/certificates/apns.p8`

```
PUSH_APNS_ENV=production
PUSH_APNS_KEY_ID=
PUSH_APNS_TEAM_ID=
PUSH_APNS_APP_BUNDLE=
PUSH_APNS_PRIVATE_KEY=
PUSH_APNS_PRIVATE_KEY_SECRET=
```

### FCM

Version 5 of this package uses the [FCM HTTP v1 API](https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages/send) and the [origami/google-auth](https://github.com/papertank/origami-google-auth) package to get oAuth tokens from service account credentials.

First set the project ID from your Firebase console:

```
PUSH_FCM_PROJECT="your-app-123abc"
```

Then generate and download your service account JSON file to generate oAuth access tokens.

1. In the Firebase console, open Settings > Service Accounts.
2. Click Generate New Private Key, then confirm by clicking Generate Key.
3. Securely store the JSON file containing the key at: `storage/app/google-auth/service-account-credentials.json`

If you want to change the loaded path you can set the `GOOGLE_AUTH_SERVICE_CREDENTIALS` environment variable, or to change other settings you can publish the google-auth config: `php artisan vendor:publish --provider="Origami\GoogleAuth\GoogleAuthServiceProvider"`

## Usage

### Device Eloquent Model

You will most likely be storing devices in your database using an Eloquent model, e.g. `App\Device`.

To have that work with this package, you just need to make sure it implements the `Origami\Push\Contracts\Device` interface.

```php
namespace App;

use Origami\Push\Contracts\Device as PushDevice;

class Device extends Model implements PushDevice {

}
```

Next, you need to add two methods to get the service - either `apns` for iOS or `fcm` for Firebase Cloud Messaging - and the device identifier.

```php
public function getPushService()
{
    switch ( $this->make ) {
        case 'apple':
        case 'ios':
        case 'iphone':
            return 'apns';
            break;
        case 'android':
            return 'fcm';
            break;
        default:
            throw new \Exception('Unable to determine push service for ' . $this->make);
    }
}

public function getPushToken()
{
    return $this->device_token;
}
```

### User Notifiable Devices

In a Laravel project, you're most likely to send a push notification to your users. See the [Laravel Docs](https://laravel.com/docs/8.x/notifications) for more information.

To get your User's devices, assuming you are using an Eloquent model above, you would just add a `routeNotificationForPush` method to your Eloquent model.

```php
public function routeNotificationForPush()
{
    $devices = $this->devices()->get();
    return $devices ? $devices->all() : [];
}
```

## Examples

### Notification Class

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Origami\Push\PushNotification;

class UserJoined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['push'];
    }

    public function toPush($notifiable)
    {
        return (new PushNotification)
                    ->setTitle('New User')
                    ->setBody($this->user->name . ' just joined')
                    ->setMeta([
                        'event' => 'NewUser',
                        'user' => $this->user->id
                    ]);
    }

}
```

### Standalone

```php
<?php

$device = new Origami\Push\Device('apns', '12346...');

$push = (new Origami\Push\PushNotification)
        ->setBody('Testing, testing, 1, 2, 3.');

app('Origami\Push\PushManager')
		->driver($device->getPushService())
        ->send($device, $push);
```

## Versions
 - v5.* - Version 5 is a breaking change that updates the config and drivers for fcm to use the new HTTP v1 API and service account credentials / oAuth.
 - v4.* - Version 4 is a breaking change that updates the config and drivers for apns and fcm.
 - v3.* - Version 3 bumps the Laravel support to include 6, 7 and 8 projects. Laravel 5.x dropped.
 - v2.* - Version 2 is a rewrite of the package to work with Laravel 5.3 notifications or standalone
 - v1.-* - Version 1 did not integrate with the notifications service of Laravel

## Author
[Papertank Limited](http://papertank.com)

## License
[View the license](http://github.com/papertank/origami-push/blob/master/LICENSE)
