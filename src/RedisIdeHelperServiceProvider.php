<?php

declare(strict_types=1);
/**
 * This file is part of http-client.
 *
 * @link     https://github.com/huangdijia/laravel-redis-ide-helper
 * @document https://github.com/huangdijia/laravel-redis-ide-helper/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Huangdijia\RedisIdeHelper;

use Illuminate\Support\ServiceProvider;

class RedisIdeHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\GenerateCommand::class,
            ]);
        }
    }
}
