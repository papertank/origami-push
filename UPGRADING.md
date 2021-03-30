# Upgrading

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