<?php

namespace Origami\Push;

use Origami\Push\PushChannel;
use Origami\Push\PushManager;
use Illuminate\Support\ServiceProvider;
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
            __DIR__.'/../config' => config_path(),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/push.php', 'push'
        );

        $this->app->singleton(PushManager::class, function ($app) {
            return new PushManager($app);
        });

        Notification::extend('push', function() {
            return new PushChannel(
                app(PushManager::class)
            );
        });
    }
}
