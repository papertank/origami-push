<?php

namespace Origami\Push\Contracts;

interface Device {

    public function getPushService();
    public function getPushToken();

}
