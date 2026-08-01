<?php

declare(strict_types=1);

namespace WooExtender\Core;

defined('ABSPATH') || exit;

use WooExtender\Services\SupplierService;
use WooExtender\Services\WarrantyService;
use WooExtender\Services\BatchService;

class Activator
{
    public static function activate(): void
    {
        self::createTables();
    }

    private static function createTables(): void
    {
        $supplierService = WooExtender::make(SupplierService::class);
        $warrantyService = WooExtender::make(WarrantyService::class);
        $batchService = WooExtender::make(BatchService::class);

        $queries = [
            $supplierService->getTableSchema(),
            $warrantyService->getTableSchema(),
            $batchService->getTableSchema(),
        ];

        if (! function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $queries && dbDelta($queries);
    }
}