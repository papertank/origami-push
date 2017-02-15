<?php

namespace Origami\Push;

use Exception;
use Illuminate\Support\Manager;
use Origami\Push\Drivers\Apns;
use Origami\Push\Drivers\Fcm;
use Origami\Push\Drivers\Gcm;

class PushManager extends Manager {

    protected function createApnsDriver()
    {
        return new Apns(
            $this->app['config']['push.apns'],
            $this->app['config']['push.apns.environment']
        );
    }

    protected function createGcmDriver()
    {
        return new Gcm(
            $this->app['config']['push.gcm']
        );
    }

    protected function createFcmDriver()
    {
        return new Fcm(
            $this->app['config']['push.fcm']
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
