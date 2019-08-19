# laravel-redis-ide-helper

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
