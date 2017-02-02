<?php

namespace Origami\Push;

use Origami\Push\PushNotification;
use Origami\Push\Contracts\Device;

abstract class Driver {

    public abstract function send(Device $device, PushNotification $notification);

}
