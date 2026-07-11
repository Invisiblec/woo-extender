<?php

namespace WooExtender\Core;

defined('ABSPATH') || exit;

class WooExtender
{
    private $instances = [];

    public static function service(string $key, string $class)
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new $class();
        }

        return self::$instances[$key];
    }
}