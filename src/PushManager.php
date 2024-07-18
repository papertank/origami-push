<?php

namespace Origami\Push;

use Exception;
use Illuminate\Support\Manager;
use Origami\GoogleAuth\GoogleAuth;
use Origami\Push\Drivers\Apns;
use Origami\Push\Drivers\Apns\ClientFactory;
use Origami\Push\Drivers\Fcm;

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
            $this->container['config']['push.fcm'],
            $this->container->make(GoogleAuth::class)
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getDefaultDriver()
    {
        throw new Exception('No default push driver');
    }
}
