<?php

declare(strict_types=1);
/**
 * This file is part of laravel-redis-ide-helper.
 *
 * @link     https://github.com/huangdijia/laravel-redis-ide-helper
 * @document https://github.com/huangdijia/laravel-redis-ide-helper/blob/3.x/README.md
 * @contact  huangdijia@gmail.com
 */

namespace Huangdijia\RedisIdeHelper\Commands;

use Composer\Autoload\ClassLoader;
use Huangdijia\RedisIdeHelper\AddRedisDocCommentVisitor;
use Illuminate\Console\Command;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Throwable;

class GenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'ide-helper:redis {filename=_ide_helper_redis.php : The path to the helper file} {--client= : The redis client, phpredis or predis.}';

    /**
     * @var string
     */
    protected $description = 'Generate autocompletion for redis.';

    public function __construct(
        protected ClassLoader $loader,
        protected Parser $parser,
        protected Standard $printer,
        protected NodeTraverser $traverser,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $filename = $this->argument('filename');
            $client = $this->option('client') ?: config('database.redis.client', 'phpredis');

            if (! $filename) {
                $filename = '_ide_helper_redis.php';
            }

            if (substr($filename, -4, 4) !== '.php') {
                $filename .= '.php';
            }

            $filename = $this->laravel->basePath($filename);

            $redisFacadePath = $this->loader->findFile('Illuminate\Support\Facades\Redis');
            $code = file_get_contents($redisFacadePath);

            $this->traverser->addVisitor(new AddRedisDocCommentVisitor($this->parser, $client));

            $stmts = $this->parser->parse($code);
            $stmts = $this->traverser->traverse($stmts);

            $code = $this->printer->prettyPrint($stmts);
            $code = "<?php\n" . ltrim($code, '?>' . PHP_EOL);

            $written = file_put_contents($filename, $code);

            if ($written !== false) {
                $this->info("A new helper file was written to {$filename}");
            } else {
                $this->error("The helper file could not be created at {$filename}");
            }
        } catch (Throwable $e) {
            $this->warn($e->getMessage());
        }
    }
}
