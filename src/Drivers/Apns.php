<?php

namespace Origami\Push\Drivers;

use Pushok\Client;
use Pushok\Payload;
use Pushok\Notification;
use Pushok\Payload\Alert;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Origami\Push\Contracts\Driver;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Events\Dispatcher;

class Apns extends Driver
{
    /**
     *
     * @var \Pushok\Client
     */
    protected $client;

    /**
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    const SANDBOX = 0;
    const PRODUCTION = 1;

    public function __construct(Client $client, Dispatcher $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    public function sendMultiple(Collection $devices, PushNotification $notification)
    {
        foreach ( $devices as $device ) {
            $this->client->addNotification($this->toApnsNotification($notification, $device->getPushToken()));
        }
        $this->client->push();
    }

    public function send(Device $device, PushNotification $notification)
    {
        $this->client->addNotification($this->toApnsNotification($notification, $device->getPushToken()));
        $this->client->push();
    }

    protected function toApnsNotification(PushNotification $notification, $token)
    {
        $alert = Alert::create();

        if ($notification->message) {
            $alert->setBody($notification->message);
        }

        $payload = Payload::create();
        $payload->setAlert($alert);

        if ($notification->badge) {
            $payload->setBadge($notification->badge);
        }

        if ($notification->sound) {
            $payload->setSound($notification->sound);
        }

        if ($notification->meta) {
            foreach ($notification->meta as $key => $value) {
                $payload->setCustomValue($key, $value);
            }
        }

        return new Notification($payload, $token);
    }

    // private function getEnvironmentHost()
    // {
    //     switch ($this->environment) {
    //         case self::SANDBOX:
    //             return 'gateway.sandbox.push.apple.com:2195';
    //             break;
    //         case self::PRODUCTION:
    //             return 'gateway.push.apple.com:2195';
    //             break;
    //         default:
    //             throw new Exception('Invalid APNS environment: ' . $this->environment);
    //     }
    // }
}
