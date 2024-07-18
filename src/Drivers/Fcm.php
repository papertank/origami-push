<?php

namespace Origami\Push\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Origami\GoogleAuth\GoogleAuth;
use Origami\Push\Contracts\Device;
use Origami\Push\Contracts\Driver;
use Origami\Push\Exceptions\InvalidConfiguration;
use Origami\Push\PushNotification;
use Origami\Push\PushNotificationResponse;

class Fcm extends Driver
{
    protected array $config;

    protected GoogleAuth $auth;

    protected Client $client;

    public function __construct(array $config, GoogleAuth $auth)
    {
        $this->config = $config;
        $this->auth = $auth;
        $this->client = $this->createClient();
    }

    protected function createClient()
    {
        return $this->auth->client([
            'base_uri' => 'https://fcm.googleapis.com/v1/',
        ], [
            'https://www.googleapis.com/auth/firebase.messaging',
        ]);
    }

    public function sendMultiple(Collection $devices, PushNotification $notification): array
    {
        $responses = [];

        foreach ($devices as $device) {
            $responses[] = $this->send($device, $notification);
        }

        return $responses;
    }

    public function send(Device $device, PushNotification $notification): PushNotificationResponse
    {
        $projectId = $this->config['project_id'];

        if (! $projectId) {
            throw InvalidConfiguration::projectIdNotSpecified();
        }

        $url = 'projects/'.$projectId.'/messages:send';

        try {
            $response = $this->client->post($url, [
                'json' => $this->getPayload($notification, $device),
            ]);
        } catch (RequestException $e) {
            return PushNotificationResponse::error([
                'token' => $device->getPushToken(),
            ], $e->getMessage());
        }

        $data = json_decode($response->getBody(), true);

        return PushNotificationResponse::success($data);
    }

    protected function getPayload(PushNotification $notification, Device $device)
    {
        $payload = [
            'message' => [
                'token' => $device->getPushToken(),
            ],
        ];

        if ($notification->isTest()) {
            $payload['validate_only'] = true;
        }

        $payload['message'] = array_merge($payload['message'], $this->getNotificationPayload($notification));

        return $payload;
    }

    protected function setPayloadApns(PushNotification $notification, array $payload)
    {
        if ($subtitle = $notification->getSubtitle()) {
            Arr::set($payload, 'apns.payload.aps.alert.subtitle', $subtitle);
        }
        if ($notification->getBadge() !== null) {
            Arr::set($payload, 'apns.payload.aps.badge', (int) $notification->getBadge());
        }
        if ($sound = $notification->getSound()) {
            Arr::set($payload, 'apns.payload.aps.sound', $sound);
        }

        return $payload;
    }

    protected function setPayloadAndroid(PushNotification $notification, array $payload)
    {
        if ( $clickAction = $notification->getExtraValue('action') ) {
            Arr::set($payload, 'android.notification.click_action', $clickAction);
        }
        if ($notification->getBadge() !== null) {
            Arr::set($payload, 'android.notification.notification_count', (int) $notification->getBadge());
        }
        if ($sound = $notification->getSound()) {
            Arr::set($payload, 'android.notification.sound', $sound);
        }

        return $payload;
    }

    protected function setPayloadData(PushNotification $notification, array $payload)
    {
        $data = $notification->getMeta();

        $payload['data'] = collect($data)->map(function ($value, $key) {
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }

            return (string) $value;
        })->all();

        return $payload;
    }

    protected function getNotificationPayload(PushNotification $notification)
    {
        $payload = [
            'notification' => array_filter([
                'title' => $notification->getTitle(),
                'body' => $notification->getBody(),
            ]),
        ];

        if (in_array('apns', $this->config['platforms'] ?? [])) {
            $payload = $this->setPayloadApns($notification, $payload);
        }

        if (in_array('android', $this->config['platforms'] ?? [])) {
            $payload = $this->setPayloadAndroid($notification, $payload);
        }

        if ($notification->getMeta()) {
            $payload = $this->setPayloadData($notification, $payload);
        }

        return $payload;
    }
}
