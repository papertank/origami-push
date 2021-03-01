<?php

namespace Origami\Push;

use Pushok\Client;
use Illuminate\Support\Arr;
use Origami\Push\PushChannel;
use Origami\Push\PushManager;
use Pushok\AuthProvider\Token;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class PushServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/push.php' => $this->app->configPath('config.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/push.php',
            'push'
        );

        $this->app->singleton(PushManager::class, function ($app) {
            return new PushManager($app);
        });

        Notification::extend('push', function () {
            return new PushChannel(
                $this->app->make(PushManager::class),
                $this->app->make(Dispatcher::class),
                $this->app['config']->get('push')
            );
        });

        $this->app->bind(Token::class, function ($app) {
            return Token::create(Arr::except($app['config']['push.apns'], 'environment'));
        });

        $this->app->bind(Client::class, function ($app) {
            $production = $this->app['config']['push.apns.environment'] == \Origami\Push\Drivers\Apns::PRODUCTION;
            return new Client($app->make(Token::class), $production);
        });
    }
}
