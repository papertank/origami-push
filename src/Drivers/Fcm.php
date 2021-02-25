<?php

namespace Origami\Push\Drivers;

use Exception;
use Illuminate\Support\Arr;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Origami\Push\Contracts\Driver;
use Illuminate\Support\Collection;
use Origami\Push\PushNotificationResponse;

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

    /**
     * @param  \Illuminate\Support\Collection  $devices
     * @param  \Origami\Push\PushNotification  $notification
     *
     * @return array
     */
    public function sendMultiple(Collection $devices, PushNotification $notification)
    {
        $responses = [];

        foreach ( $devices as $device ) {
            $responses[] = $this->send($device, $notification);
        }

        return $responses;
    }

    /**
     * @param  \Origami\Push\Contracts\Device  $device
     * @param  \Origami\Push\PushNotification  $notification
     *
     * @return \Origami\Push\PushNotificationResponse
     */
    public function send(Device $device, PushNotification $notification)
    {
        $url = 'https://' . $this->getEnvironmentHost();

        $fields = [
            'to' => $device->getPushToken(),
            'data' => array_filter([
                'title' => $notification->getTitle(),
                'message' => $notification->getBody(),
                'badge' => $notification->getBadge(),
                'sound' => $notification->getSound(),
                'action_key' => $notification->getCategory(),
                'data' => $notification->getMeta(),
            ]),
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
            return PushNotificationResponse::error([], curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return PushNotificationResponse::success();
    }

    private function getEnvironmentHost()
    {
        return 'fcm.googleapis.com/fcm/send';
    }
}
