<?php

namespace Origami\Push;

use Illuminate\Support\Arr;
use Origami\Push\Contracts\Device;
use Origami\Push\PushNotification;
use Illuminate\Support\Collection;
use Origami\Push\Contracts\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Config\Repository;

class PushChannel
{
    /**
     * @var \Origami\Push\PushManager
     */
    private $manager;

    public function __construct(PushManager $manager, array $config)
    {
        $this->manager = $manager;
        $this->config = $config;
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

        if ($devices->isEmpty()) {
            return;
        }

        $push = $notification->toPush($notifiable);

        if (is_string($push)) {
            $message = $push;
            $push = new PushNotification($message);
        }

        $services = $devices->groupBy(function($device) {
            return $device->getPushService();
        });

        foreach ($services as $service => $devices) {
            $driver = $this->manager->driver($devices->first()->getPushService());
            $this->logNotifications($driver, $devices, $push);
            $this->sendNotifications($driver, $devices, $push);
        }
    }

    protected function sendNotification(Device $device, PushNotification $push)
    {
        if (! Arr::get($this->config, 'push.enabled')) {
            return false;
        }

        return $this->manager->driver($device->getPushService())->send($device, $push);
    }

    protected function sendNotifications(Driver $driver, Collection $devices, PushNotification $push)
    {
        if (! Arr::get($this->config, 'push.enabled')) {
            return false;
        }

        return $driver->sendMultiple($devices, $push);
    }

    protected function logNotification(Device $device, PushNotification $push)
    {
        $key = $device->getPushService().':'.$device->getPushToken();
        Log::debug('Push Notification to '.$key, $push->toArray());
    }

    protected function logNotifications(Driver $driver, Collection $devices, PushNotification $push)
    {
        if (Arr::get($this->config, 'push.log')) {
            foreach ( $devices as $device ) {
                $this->logNotification($device, $push);
            }
        }
    }

    /**
     * @param $notifiable
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDevices($notifiable)
    {
        $devices = $notifiable->routeNotificationFor('push');

        if (! $devices) {
            return new Collection;
        }

        if ( ! $devices instanceof Collection ) {
            $devices = new Collection($devices);
        }

        return $devices->filter(function ($device) {
            return ($device instanceof \Origami\Push\Contracts\Device);
        });
    }
}
