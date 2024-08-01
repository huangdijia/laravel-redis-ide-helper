# laravel-redis-ide-helper

[![Latest Test](https://github.com/huangdijia/laravel-redis-ide-helper/workflows/tests/badge.svg)](https://github.com/huangdijia/laravel-redis-ide-helper/actions)
[![Latest Stable Version](https://img.shields.io/packagist/v/huangdijia/laravel-redis-ide-helper)](https://packagist.org/packages/huangdijia/laravel-redis-ide-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/huangdijia/laravel-redis-ide-helper)](https://packagist.org/packages/huangdijia/laravel-redis-ide-helper)
[![GitHub license](https://img.shields.io/github/license/huangdijia/laravel-redis-ide-helper)](https://github.com/huangdijia/laravel-redis-ide-helper)

## Installation

```shell
composer require huangdijia/laravel-redis-ide-helper --dev
```

Register for Lumen, add Command to `app/Console/Kernel.php`

```php
protected $commands = [
    // ...
    \Huangdijia\RedisIdeHelper\Commands\GenerateCommand::class,
    // ...
];
```

## Usage

```shell
php artisan ide-helper:redis
```
