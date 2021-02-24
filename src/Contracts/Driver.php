<?php

namespace Origami\Push\Contracts;

use Origami\Push\PushNotification;
use Illuminate\Support\Collection;

abstract class Driver {

    public abstract function send(Device $device, PushNotification $notification);
    public abstract function sendMultiple(Collection $devices, PushNotification $notification);

}
