<?php

declare(strict_types=1);

namespace WooExtender\Core;

use WooExtender\Providers\AppServiceProvider;
use WooExtender\Providers\HookServiceProvider;
use WooExtender\Providers\RepositoryServiceProvider;

defined('ABSPATH') || exit;

class WooExtender
{
    private static ?Container $container = null;

    private static array $providers = [
        AppServiceProvider::class,
        RepositoryServiceProvider::class,
        HookServiceProvider::class,
    ];

    public static function getContainer(): Container
    {
        if (self::$container === null) {
            self::$container = new Container();
        }
        return self::$container;
    }

    public static function register(): void
    {
        $container = self::getContainer();

        foreach (self::$providers as $providerClass) {
            $provider = new $providerClass();
            if (method_exists($provider, 'register')) {
                $provider->register($container);
            }
        }
    }

    public static function make(string $class)
    {
        return self::getContainer()->get($class);
    }

    public static function boot(): void {}
}