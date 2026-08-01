<?php

declare(strict_types=1);

namespace WooExtender\Core;

defined('ABSPATH') || exit;

class WooExtender
{
    private static ?Container $container = null;

    public static function getContainer(): Container
    {
        if (self::$container === null) {
            self::$container = new Container();
        }
        return self::$container;
    }

    public static function make(string $class)
    {
        return self::getContainer()->get($class);
    }
}
