<?php

declare(strict_types=1);

namespace WooExtender\Providers;

use WooExtender\Core\Container;
use WooExtender\Interfaces\ServiceProviderInterface;
use WooExtender\Admin\Menu\AdminMenu;
use WooExtender\Controllers\AjaxController;
use WooExtender\Admin\ProductDataTabs\ProductDataTab;
use WooExtender\Controllers\SupplierController;
use WooExtender\Controllers\WarrantyController;
use WooExtender\Controllers\BatchController;
use WooExtender\Services\InventoryService;

class HookServiceProvider implements ServiceProviderInterface
{
    protected array $classes = [
        AdminMenu::class,
        AjaxController::class,
        ProductDataTab::class,
        SupplierController::class,
        WarrantyController::class,
        BatchController::class,
        InventoryService::class,
    ];

    public function register(Container $container): void
    {
        foreach ($this->classes as $class) {
            $container->get($class);
        }
    }
}