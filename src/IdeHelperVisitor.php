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

use PhpParser\Comment\Doc;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use Redis;
use ReflectionClass;

class IdeHelperVisitor extends NodeVisitorAbstract
{
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function afterTraverse(array $nodes)
    {
        /** @var PhpParser\Node\Stmt\Class_ */
        $class = &$nodes[0]->stmts[0];
        $class->setDocComment(new Doc($this->getRedisDocComment()));
        $class->stmts = [];

        return $nodes;
    }

    protected function getRedisDocComment(): string
    {
        if (class_exists(\Predis\ClientInterface::class)) {
            $rc = new ReflectionClass(\Predis\ClientInterface::class);
            return $rc->getDocComment() ?: '';
        }

        if (class_exists(Redis::class)) {
            return "/**\n * @mixin \\Redis\n */";
        }

        return '';
    }
}
