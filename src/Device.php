<?php

namespace Origami\Push;

use Origami\Push\Contracts\Device as DeviceContract;

class Device implements DeviceContract {

    protected $service;
    protected $token;

	public function __construct($service, $token)
	{
		$this->service = $service;
		$this->token = $token;
	}

	public function getPushService()
	{
		return $this->service;
	}

    public function getPushToken()
    {
    	return $this->token;
    }

}
