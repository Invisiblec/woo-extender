<?php

declare(strict_types=1);

namespace WooExtender\Interfaces;

use WooExtender\Core\Container;

defined('ABSPATH') || exit;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}