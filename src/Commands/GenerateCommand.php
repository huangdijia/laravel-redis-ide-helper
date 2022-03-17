<?php

declare(strict_types=1);
/**
 * This file is part of http-client.
 *
 * @link     https://github.com/huangdijia/laravel-redis-ide-helper
 * @document https://github.com/huangdijia/laravel-redis-ide-helper/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Huangdijia\RedisIdeHelper\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'ide-helper:redis';

    /**
     * @var string
     */
    protected $description = 'Generate autocompletion for redis';

    public function handle()
    {
        try {
            $path = app()->basePath() . '/_ide_helper_redis.php';
            $content = file_get_contents($this->getStub());

            $this->files->put($path, $content);

            $this->info('Redis ide helper created.[' . $path . ']');
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/_ide_helper_redis.stub';
    }
}
