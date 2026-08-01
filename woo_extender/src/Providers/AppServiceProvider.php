<?php

declare(strict_types=1);

namespace WooExtender\Providers;

use Override;
use WooExtender\Core\Container;
use WooExtender\Interfaces\ServiceProviderInterface;
use WooExtender\Services\AccessController;
use WooExtender\Services\BatchService;
use WooExtender\Services\InventoryService;
use WooExtender\Services\SupplierService;
use WooExtender\Services\WarrantyService;

defined('ABSPATH') || exit;

class AppServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(AccessController::class);
        $container->set(BatchService::class);
        $container->set(InventoryService::class);
        $container->set(SupplierService::class);
        $container->set(WarrantyService::class);
    }
}