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
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class AddRedisDocCommentVisitor extends NodeVisitorAbstract
{
    public function __construct(protected Parser $parser, protected string $client = 'phpredis')
    {
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
        switch (strtolower($this->client)) {
            case 'predis':
                $rc = new ReflectionClass(\Predis\ClientInterface::class);
                return $rc->getDocComment() ?: '';
            case 'phpredis':
                $rc = new ReflectionClass(\Illuminate\Redis\Connections\PhpRedisConnection::class);

                $docComment = "/**\n";

                foreach ($rc->getMethods() as $method) {
                    if (
                        $method->isConstructor()
                        || ! $method->isPublic()
                        || str_starts_with($method->getName(), '_')
                    ) {
                        continue;
                    }

                    $docComment .= $this->parseMethod($method);
                }
                $docComment .= " */\n";

                return $docComment;
        }

        return '';
    }

    private function parseMethod(ReflectionMethod $method): string
    {
        $docComment = '';
        $docComment .= ' * @method static ' . $method->getName() . '(';

        $params = [];

        foreach ($method->getParameters() as $parameter) {
            $params[] = $this->parseMethodParameter($parameter);
        }

        $docComment .= implode(', ', $params) . ")\n";

        if ($method->hasReturnType()) {
            $docComment = str_replace('@method static', '@method static ' . $method->getReturnType(), $docComment);
        } elseif ($phpdoc = $method->getDocComment()) {
            preg_match('/@return\s+([^\s]+)/', $phpdoc, $matches);
            if (isset($matches[1])) {
                $docComment = str_replace('@method static', '@method static ' . $matches[1], $docComment);
            }
        }

        return $docComment;
    }

    private function parseMethodParameter(ReflectionParameter $parameter): string
    {
        $paramStr = '';

        if ($parameter->hasType()) {
            $paramStr .= $parameter->getType() . ' ';
        }

        if ($parameter->isPassedByReference()) {
            $paramStr .= '&';
        }

        if ($parameter->isVariadic()) {
            $paramStr .= '...';
        }

        $paramStr .= '$' . $parameter->getName();

        if ($parameter->isOptional() && $defaultValue = $this->parseParameterDefaultValue($parameter)) {
            $paramStr .= ' = ' . $defaultValue;
        }

        return $paramStr;
    }

    private function parseParameterDefaultValue(ReflectionParameter $parameter): string
    {
        if (! $parameter->isDefaultValueAvailable()) {
            return '';
        }

        $defaultValue = $parameter->getDefaultValue();

        if (is_array($defaultValue)) {
            return '[]';
        }

        if (is_string($defaultValue)) {
            return "'{$defaultValue}'";
        }

        if (is_bool($defaultValue)) {
            return $defaultValue ? 'true' : 'false';
        }

        if (is_null($defaultValue)) {
            return 'null';
        }

        return (string) $defaultValue;
    }
}
