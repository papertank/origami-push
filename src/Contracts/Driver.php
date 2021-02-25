<?php

namespace Origami\Push\Contracts;

use Origami\Push\PushNotificationResponse;
use Illuminate\Support\Collection;
use Origami\Push\PushNotification;

abstract class Driver
{

    /**
     *
     * @param Device $device
     * @param PushNotification $notification
     *
     * @return PushNotificationResponse
     */
    abstract public function send(Device $device, PushNotification $notification);

    /**
     *
     * @param Collection $devices
     * @param PushNotification $notification
     * @return array
     */
    abstract public function sendMultiple(Collection $devices, PushNotification $notification);
}
