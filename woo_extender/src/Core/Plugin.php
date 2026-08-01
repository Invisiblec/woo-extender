<?php

declare(strict_types=1);

namespace WooExtender\Core;

defined('ABSPATH') || exit;

use WooExtender\Admin\Menu\AdminMenu;
use WooExtender\Controllers\AjaxController;
use WooExtender\Admin\ProductDataTabs\ProductDataTab;
use WooExtender\Controllers\SupplierController;
use WooExtender\Controllers\WarrantyController;
use WooExtender\Controllers\BatchController;
use WooExtender\Services\InventoryService;

class Plugin
{
    public function run(): void
    {
        if (!is_admin()) return;

        new AdminMenu();
        new AjaxController();
        new ProductDataTab();
        if (class_exists(SupplierController::class)) new SupplierController();
        if (class_exists(WarrantyController::class)) new WarrantyController();
        if (class_exists(BatchController::class)) new BatchController();
        new InventoryService();
    }
}
