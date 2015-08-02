<?php 

namespace Origami\Push\Device;

interface DeviceInterface {

    public function push($token, $data, $params);

}