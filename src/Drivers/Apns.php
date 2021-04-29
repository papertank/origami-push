<?php

namespace Origami\Push\Drivers;

use Pushok\Client;
use Pushok\Payload;
use Pushok\Response;
use Pushok\Notification;
use Pushok\Payload\Alert;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Origami\Push\Contracts\Driver;
use Illuminate\Support\Collection;
use Origami\Push\PushNotificationResponse;
use Origami\Push\Drivers\Apns\ClientFactory;

class Apns extends Driver
{
    const SANDBOX = 0;
    const PRODUCTION = 1;
    /**
     * @var \Origami\Push\Drivers\Apns\ClientFactory
     */
    protected $factory;

    public function __construct(ClientFactory $factory)
    {
        $this->factory = $factory;
    }

    public function sendMultiple(Collection $devices, PushNotification $notification)
    {
        $client = $this->factory->instance();

        foreach ($devices as $device) {
            $client->addNotification($this->toApnsNotification($notification, $device->getPushToken()));
        }

        $responses = $client->push();

        return array_map(function($response) {
            return $this->toPushNotificationResponse($response);
        }, $responses);
    }

    public function send(Device $device, PushNotification $notification)
    {
        $client = $this->factory->instance();

        $client->addNotification($this->toApnsNotification($notification, $device->getPushToken()));
        $responses = $client->push();

        return $this->toPushNotificationResponse(end($responses));
    }

    protected function toPushNotificationResponse($response)
    {
        if ( ! $response ) {
            return PushNotificationResponse::error();
        }

        if ( $response->getStatusCode() == Response::APNS_SUCCESS ) {
            return PushNotificationResponse::success(['id' => $response->getApnsId(), 'token' => $response->getDeviceToken()]);
        }

        return PushNotificationResponse::error(['id' => $response->getApnsId(), 'token' => $response->getDeviceToken(), 'reason' => $response->getErrorReason()], $response->getErrorDescription());
    }

    protected function toApnsNotification(PushNotification $notification, $token)
    {
        $alert = Alert::create();

        $body = $notification->getBody();
        $title = $notification->getTitle();

        if ( empty($body) && ! empty($title) ) {
            $alert->setBody($title);
        } else if ( ! empty($title) ) {
            $alert->setBody($body);
            $alert->setTitle($title);
        } else {
            $alert->setBody($body);
        }

        if ( $subtitle = $notification->getSubtitle() ) {
            $alert->setSubtitle($subtitle);
        }

        $payload = Payload::create();
        $payload->setAlert($alert);

        if ($badge = $notification->getBadge()) {
            $payload->setBadge($badge);
        }

        if ($category = $notification->getCategory()) {
            $payload->setCategory($category);
        }

        if ($sound = $notification->getSound()) {
            $payload->setSound($sound);
        }

        if ($meta = $notification->getMeta()) {
            foreach ($meta as $key => $value) {
                $payload->setCustomValue($key, $value);
            }
        }

        return new Notification($payload, $token);
    }

}
