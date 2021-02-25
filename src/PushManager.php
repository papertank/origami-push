<?php

namespace Origami\Push;

use Exception;
use Pushok\Client;
use Origami\Push\Drivers\Fcm;
use Origami\Push\Drivers\Gcm;
use Origami\Push\Drivers\Apns;
use Illuminate\Support\Manager;
use Illuminate\Contracts\Events\Dispatcher;

class PushManager extends Manager
{
    protected function createApnsDriver()
    {
        return new Apns(
            $this->container->make(Client::class)
        );
    }

    protected function createGcmDriver()
    {
        return new Gcm(
            $this->container['config']['push.gcm']
        );
    }

    protected function createFcmDriver()
    {
        return new Fcm(
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
