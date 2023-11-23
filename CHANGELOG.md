# Changelog

All notable changes will be documented in this file.

## Unreleased

## 4.4.0 - 2023-11-23
- Added data to FCM notification type.

## 4.3.0 - 2023-02-16
- Added Laravel 10.x support

## 4.2.0 - 2022-05-13
- Added Laravel 9.x support

## 4.1.1 - 2021-06-03
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
