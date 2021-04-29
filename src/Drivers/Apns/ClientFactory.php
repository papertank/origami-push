<?php

namespace Origami\Push\Drivers\Apns;

use Pushok\Client;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class ClientFactory
{
    /**
     * The number of seconds to cache the client
     *
     * @var int
     */
    const TTL = 1200;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    public function __construct(Container $app, Repository $cache)
    {
        $this->app = $app;
        $this->cache = $cache;
    }

    public function instance()
    {
        return $this->cache->remember(Client::class, Carbon::now()->addSeconds(static::TTL), function() {
            return $this->app->make(Client::class);
        });
    }

}
