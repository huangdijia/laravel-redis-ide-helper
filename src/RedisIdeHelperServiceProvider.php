<?php

declare(strict_types=1);
/**
 * This file is part of laravel-redis-ide-helper.
 *
 * @link     https://github.com/huangdijia/laravel-redis-ide-helper
 * @document https://github.com/huangdijia/laravel-redis-ide-helper/blob/3.x/README.md
 * @contact  huangdijia@gmail.com
 */

namespace Huangdijia\RedisIdeHelper;

use Composer\Autoload\ClassLoader;
use Huangdijia\RedisIdeHelper\Commands\GenerateCommand;
use Illuminate\Support\ServiceProvider;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;

class RedisIdeHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->registerCommands();

        $this->app->when(GenerateCommand::class)
            ->needs(ClassLoader::class)
            ->give(function () {
                $loaders = spl_autoload_functions();

                foreach ($loaders as $loader) {
                    if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                        return $loader[0];
                    }
                }

                throw new RuntimeException('Composer loader not found.');
            });

        $this->app->when(GenerateCommand::class)
            ->needs(Parser::class)
            ->give(function () {
                return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
            });
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
    }
}
