<?php

declare(strict_types=1);

namespace WooExtender\Core;

defined('ABSPATH') || exit;
class Plugin
{
    public function run(): void
    {
        if (!is_admin()) return;

        WooExtender::register();
    }
}