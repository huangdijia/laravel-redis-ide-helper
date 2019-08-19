<?php

namespace Huangdijia\RedisIdeHelper\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Redis;

class GenerateCommand extends GeneratorCommand
{
    protected $signature   = 'ide-helper:redis';
    protected $description = 'Generate autocompletion for redis';

    protected function getStub()
    {
        return __DIR__ . '/stubs/_ide_helper_redis.stub';
    }

    public function handle()
    {
        try {
            $path    = app()->basePath() . '/_ide_helper_redis.php';
            $content = file_get_contents($this->getStub());

            $this->files->put($path, $content);

            $this->info('Redis ide helper created.[' . $path . ']');
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }
}
