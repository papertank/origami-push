<?php

namespace Origami\Push;

use Exception;
use Origami\Push\Drivers\Fcm;
use Origami\Push\Drivers\Apns;
use Illuminate\Support\Manager;
use Origami\Push\Drivers\Apns\ClientFactory;

class PushManager extends Manager
{
    protected function createApnsDriver()
    {
        return new Apns(
            $this->container->make(ClientFactory::class)
        );
    }

    protected function createFcmDriver()
    {
        return new Fcm(
            $this->container->make(\GuzzleHttp\Client::class),
            $this->container['config']['push.fcm']
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     * @throws \Exception
     */
    public function getDefaultDriver()
    {
        throw new Exception('No default push driver');
    }
}
