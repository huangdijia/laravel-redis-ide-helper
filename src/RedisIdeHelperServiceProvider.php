<?php

namespace Huangdijia\RedisIdeHelper;

use Illuminate\Support\ServiceProvider;

class RedisIdeHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\GenerateCommand::class,
            ]);
        }
    }
}