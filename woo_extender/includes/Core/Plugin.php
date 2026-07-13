<?php

namespace WooExtender\Core;

defined('ABSPATH') || exit;

use WooExtender\Admin\Menu\AdminMenu;
use WooExtender\Admin\Controller\AjaxController;
use WooExtender\Admin\ProductDataTabs\ProductDataTab;
use WooExtender\Admin\Controller\SupplierController;
use WooExtender\Admin\Controller\WarrantyController;
use WooExtender\Admin\Controller\BatchController;
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