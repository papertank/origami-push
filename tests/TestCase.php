<?php

namespace Origami\Push\Tests;

use Illuminate\Encryption\Encrypter;
use Origami\Push\PushServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PushServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('push.apns.private_key_path', __DIR__.'/certificates/apns.p8');

        $app['config']->set('app.key', 'base64:' . base64_encode(
            Encrypter::generateKey($app['config']['app.cipher'])
        ));
    }
}
