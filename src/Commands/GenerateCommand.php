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
use Huangdijia\RedisIdeHelper\IdeHelperVisitor;
use Illuminate\Console\Command;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;
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

            $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
            $printer = new Standard();
            $traverser = new NodeTraverser();
            $classLoader = $this->findLoader();

            $redisFacadePath = $classLoader->findFile('Illuminate\Support\Facades\Redis');
            $code = file_get_contents($redisFacadePath);
            $stmts = $parser->parse($code);
            $traverser->addVisitor(new IdeHelperVisitor($parser, $client));
            $stmts = $traverser->traverse($stmts);
            $code = $printer->prettyPrint($stmts);

            // TODO: Unknown reason
            $code = "<?php\n" . $code;
            $code = ltrim($code, '?>' . PHP_EOL);

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

    private function findLoader(): ClassLoader
    {
        $loaders = spl_autoload_functions();

        foreach ($loaders as $loader) {
            if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                return $loader[0];
            }
        }

        throw new RuntimeException('Composer loader not found.');
    }
}
