<?php

namespace Origami\Push;

use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Illuminate\Support\Facades\Log;
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

        if (! $devices) {
            return;
        }

        $push = $notification->toPush($notifiable);

        if (is_string($push)) {
            $message = $push;
            $push = new PushNotification($message);
        }

        foreach ($devices as $device) {
            $this->logNotification($device, $push);
            $this->sendNotification($device, $push);
        }
    }

    protected function sendNotification($device, $push)
    {
        if (! config('push.enabled')) {
            return false;
        }

        return $this->manager->driver($device->getPushService())
                    ->send($device, $push);
    }

    protected function logNotification($device, $push)
    {
        if (config('push.log')) {
            $key = $device->getPushService().':'.$device->getPushToken();
            Log::debug('Push Notification to '.$key, $push->toArray());
        }
    }

    protected function getDevices($notifiable)
    {
        $devices = $notifiable->routeNotificationFor('push');

        if (! $devices) {
            return false;
        }

        if (! is_array($devices)) {
            $devices = [
                $devices
            ];
        }

        return array_filter($devices, function ($device) {
            return ($device instanceof \Origami\Push\Contracts\Device);
        });
    }
}
