# laravel-redis-ide-helper

[![Latest Test](https://github.com/huangdijia/laravel-redis-ide-helper/workflows/tests/badge.svg)](https://github.com/huangdijia/laravel-redis-ide-helper/actions)
[![Latest Stable Version](https://poser.pugx.org/huangdijia/laravel-redis-ide-helper/version.png)](https://packagist.org/packages/huangdijia/laravel-redis-ide-helper)
[![Total Downloads](https://poser.pugx.org/huangdijia/laravel-redis-ide-helper/d/total.png)](https://packagist.org/packages/huangdijia/laravel-redis-ide-helper)
[![GitHub license](https://img.shields.io/github/license/huangdijia/laravel-redis-ide-helper)](https://github.com/huangdijia/laravel-redis-ide-helper)


## Installation

```bash
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

```bash
php artisan ide-helper:redis
```
