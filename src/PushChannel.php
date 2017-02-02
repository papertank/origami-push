<?php

namespace Origami\Push;

use Illuminate\Notifications\Notification;

class PushChannel
{
    /**
     * @var \Origami\Push\PushManager
     */
    private $manager;

    public function __construct(PushManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $devices = $this->getDevices($notifiable);

        if ( ! $devices ) {
            return;
        }

        $push = $notification->toPush($notifiable);

        if ( is_string($push) ) {
            $message = $push;
            $push = new PushNotification($message);
        }

        foreach ( $devices as $device ) {
            $this->manager->driver($device->getPushService())
                    ->send($device, $push);
        }
    }

    private function getDevices($notifiable)
    {
        $devices = $notifiable->routeNotificationFor('push');

        if ( ! $devices ) {
            return false;
        }

        if ( ! is_array($devices) ) {
            $devices = [
                $devices
            ];
        }

        return array_filter($devices, function($device) {
            return ( $device instanceof \Origami\Push\Contracts\Device );
        });
    }

}
