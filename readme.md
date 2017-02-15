# Origami Push - Laravel Push Notifications

This package adds a push notification channel for notifications in Laravel 5.3 or greater projects.

For more on notification channels, visit [https://laravel.com/docs/5.3/notifications](https://laravel.com/docs/5.3/notifications)

## Installation

Install this package through Composer.

```
composer require origami/push
```

### Requirements

This package is designed to work with Laravel >= 5.3 currently.

### Service Provider

As standard, there is a Laravel 5 is a service provider you can make use of to automatically prepare the bindings.

```php

// app/config/app.php

‘providers’ => [
    ...
    Origami\Api\ApiServiceProvider::class
];
```

### Configuration

There are some API configuration options that you’ll want to overwrite. First, publish the default configuration.

```bash
php artisan vendor:publish --provider="Origami\Api\ApiServiceProvider"
```

This will add a new configuration file to: `config/push.php`.

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

Next, you need to add two methods to get the service - either `apns` for iOS, `gcm` for Google Cloud Messaging or `fcm` for Firebase Cloud Messaging - and the device identifier.

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

In Laravel 5.3, you're most likely to send a push notification to your users. See the [Laravel Docs](https://laravel.com/docs/5.3/notifications) for more information. 

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
                    ->message($this->user->name . ' just joined')
                    ->meta([
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
        ->message('Testing, testing, 1, 2, 3.');

app('Origami\Push\PushManager')
		->driver($device->getPushService())
        ->send($device, $push);
```

## TODO

- Improve readme / docs

## CHANGELOG

- 2.0.2 - Added FCM (Firebase Cloud Messaging) driver

## Versions
 - v2.* - Version 2 is a rewrite of the package to work with Laravel 5.3 notifications or standalone 
 - v1.-* - Version 1 did not integrate with the notifications service of Laravel

## Author
[Papertank Limited](http://papertank.co.uk)

## License
[View the license](http://github.com/papertank/origami-push/blob/master/LICENSE)