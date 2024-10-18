<?php

namespace Origami\Push\Drivers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Origami\Push\Contracts\Device;
use Origami\Push\Contracts\Driver;
use Origami\Push\PushNotification;
use Origami\Push\PushNotificationResponse;

class Fcm extends Driver
{
    const NOTIFICATION = 'notification';

    const DATA_MESSAGE = 'data';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    public function __construct(Client $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function sendMultiple(Collection $devices, PushNotification $notification)
    {
        $responses = [];
        $url = 'https://'.$this->getEnvironmentHost();
        $headers = [
            'Authorization' => 'key='.Arr::get($this->config, 'key'),
            'Content-Type' => 'application/json',
        ];

        foreach ($devices->chunk(1000) as $devices) {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $this->getPayload($notification, $devices->all()),
            ]);
            $data = json_decode($response->getBody(), true);

            $results = isset($data['results']) ? $data['results'] : [];

            if ($results) {
                foreach ($results as $result) {
                    if (isset($result['error'])) {
                        $responses[] = PushNotificationResponse::error($result, $result['error']);
                    } else {
                        $responses[] = PushNotificationResponse::success($result);
                    }
                }
            }
        }

        return $responses;
    }

    /**
     * @return \Origami\Push\PushNotificationResponse
     */
    public function send(Device $device, PushNotification $notification)
    {
        $url = 'https://'.$this->getEnvironmentHost();

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'key='.Arr::get($this->config, 'key'),
                'Content-Type' => 'application/json',
            ],
            'json' => $this->getPayload($notification, $device),
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['failure']) && ($data['failure'] > 0)) {
            return PushNotificationResponse::error(array_merge(Arr::get($data, 'results.0', []), [
                'token' => $device->getPushToken(),
            ]), Arr::get($data, 'results.0.error'));
        }

        if ( isset($data['error']) && $data['error'] ) {
            return PushNotificationResponse::error(array_merge($data, [
                'token' => $device->getPushToken(),
            ]), $data['error']);
        }

        return PushNotificationResponse::success(Arr::get($data, 'results.0', []));
    }

    protected function getPayload(PushNotification $notification, $device)
    {
        $payload = [];

        if (is_array($device)) {
            $payload['registration_ids'] = array_map(function ($device) {
                return $device->getPushToken();
            }, $device);
        } else {
            $payload['to'] = $device->getPushToken();
        }

        $type = Arr::get($this->config, 'type', self::NOTIFICATION);

        switch ($type) {
            case self::NOTIFICATION:
                $payload = array_merge($this->getNotificationPayload($notification), $payload);
                break;
            case self::DATA_MESSAGE:
                $payload = array_merge($this->getDataMessagePayload($notification), $payload);
                break;
            default:
                throw new Exception('Unknown message type - '.$type);
        }

        return $payload;
    }

    protected function getNotificationPayload(PushNotification $notification)
    {
        $payload = [
            'notification' => array_filter([
                'title' => $notification->getTitle(),
                'body' => $notification->getBody(),
                'sound' => $notification->getSound(),
                'click_action' => $notification->getExtraValue('action'),
                'notification_count' => $notification->getBadge(),
            ]),
        ];

        if ($data = $notification->getMeta()) {
            $payload['data'] = $data;
        }

        return $payload;
    }

    protected function getDataMessagePayload(PushNotification $notification)
    {
        return [
            'data' => [
                'message' => $notification->getBody(),
                'badge' => $notification->getBadge(),
                'sound' => $notification->getSound(),
                'action_key' => $notification->getExtraValue('action'),
                'data' => $notification->getMeta() ?: [],
            ],
        ];
    }

    private function getEnvironmentHost()
    {
        return 'fcm.googleapis.com/fcm/send';
    }
}
