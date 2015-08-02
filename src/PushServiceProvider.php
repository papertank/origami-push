<?php 

namespace Origami\Push;

use Illuminate\Support\ServiceProvider;
use Origami\Push\Device\Android;
use Origami\Push\Device\Apple;

class PushServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
            __DIR__.'/../config/push.php' => config_path('push.php'),
        ]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
            __DIR__.'/../config/push.php', 'push'
        );

        $this->app->bind('push.iphone', function($app)
        {
            $config = $app['config']->get('push.apple', []);

            return new Apple($config);
        });

        $this->app->bind('push.android', function($app)
        {
            $config = $app['config']->get('push.android', []);

            return new Android($config);
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
