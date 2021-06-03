# Changelog

All notable changes will be documented in this file.

## Unreleased
- Fixed issue with PushChannel notifiable / getDevices single device.

## 4.1.0 - 2021-04-29
- Added new Apns\ClientFactory to fetch / set cached Pushok\Client
- Updated Apns driver to use new ClientFactory (fixes APNS ExpiredProviderToken errors using queue).

## 4.0.0
- Updated apns driver to use edamov/pushok package for sending
- Apns extra attributes now included outside 'aps'.
- Updated apns config with pushok keys.
- Updated fcm driver to support both standard notifications and custom data messages.
- Updated fcm driver to use Guzzle rather than curl.
- Removed gcm driver (endpoint removed by Google 2019-05-29).
- Added sendMultiple method to Driver contract.
- Deprecated message, badge and meta methods of Notification class - use setter methods instead.
- Added PushNotificationResponse object value for handling / parsing responses.
