<?php

namespace Speso\Ussd;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Speso\Ussd\Commands\ActionCommand;
use Speso\Ussd\Commands\ActionMakeCommand;
use Speso\Ussd\Commands\ConfiguratorCommand;
use Speso\Ussd\Commands\ConfiguratorMakeCommand;
use Speso\Ussd\Commands\DecisionCommand;
use Speso\Ussd\Commands\DecisionMakeCommand;
use Speso\Ussd\Commands\ExceptionHandlerCommand;
use Speso\Ussd\Commands\ExceptionHandlerMakeCommand;
use Speso\Ussd\Commands\ResponseCommand;
use Speso\Ussd\Commands\ResponseMakeCommand;
use Speso\Ussd\Commands\StateCommand;
use Speso\Ussd\Commands\StateMakeCommand;

class UssdServiceProvider extends ServiceProvider
{
    /** @return void */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /** @return void */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ussd.php', 'ussd');
    }

    /** @return void */
    protected function bootForConsole()
    {
        $this->publishes([
            __DIR__.'/../config/ussd.php' => config_path('ussd.php'),
        ], 'ussd-config');

        $this->commands([
            StateCommand::class,
            StateMakeCommand::class,
            ActionCommand::class,
            ActionMakeCommand::class,
            ResponseCommand::class,
            ResponseMakeCommand::class,
            DecisionCommand::class,
            DecisionMakeCommand::class,
            ConfiguratorCommand::class,
            ConfiguratorMakeCommand::class,
            ExceptionHandlerCommand::class,
            ExceptionHandlerMakeCommand::class,
        ]);

        AboutCommand::add('USSD', [
            'Namespace' => config('ussd.namespace'),
            'Record Store' => config('ussd.record_store') ?? config('cache.default'),
        ]);
    }
}
