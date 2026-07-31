<?php

namespace Speso\Ussd\Tests;

use Orchestra\Testbench\TestCase as Testbench;
use Speso\Ussd\UssdServiceProvider;

abstract class TestCase extends Testbench
{
    /**
     * Tell Testbench to use this package.
     *
     * @param $app
     *
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [UssdServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
