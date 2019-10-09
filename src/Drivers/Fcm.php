<?php

namespace Origami\Push\Drivers;

use Exception;
use Origami\Push\Driver;
use Illuminate\Support\Arr;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;

class Fcm extends Driver
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function send(Device $device, PushNotification $notification)
    {
        $url = 'https://' . $this->getEnvironmentHost();

        $fields = [
            'to' => $device->getPushToken(),
            'data' => [
                'message' => data_get($notification, 'message'),
                'badge' => data_get($notification, 'badge'),
                'sound' => data_get($notification, 'sound'),
                'action_key' => data_get($notification, 'action'),
                'data' => $notification->meta,
            ],
        ];

        $headers = [
            'Authorization: key=' . Arr::get($this->config, 'key'),
            'Content-Type: application/json'
        ];
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception('Push failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return true;
    }

    private function getEnvironmentHost()
    {
        return 'fcm.googleapis.com/fcm/send';
    }
}
