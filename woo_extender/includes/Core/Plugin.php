<?php

namespace WooExtender\Core;

defined('ABSPATH') || exit;

use WooExtender\Admin\Menu\AdminMenu;
use WooExtender\Admin\ProductDataTabs\ProductDataTab;
use WooExtender\Admin\Controller\SupplierController;
use WooExtender\Admin\Controller\WarrantyController;
use WooExtender\Admin\Controller\BatchController;

class Plugin
{
    public function run(): void
    {
        if (!is_admin()) return;

        new ProductDataTab();
        new AdminMenu();
        if (class_exists(SupplierController::class)) new SupplierController();
        if (class_exists(WarrantyController::class)) new WarrantyController();
        if (class_exists(BatchController::class)) new BatchController();
    }
}
