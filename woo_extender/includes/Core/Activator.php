<?php

namespace WooExtender\Core;

defined('ABSPATH') || exit;

use WooExtender\Services\SupplierService as Supplier;
use WooExtender\Services\WarrantyService as Warranty;
use WooExtender\Models\Batch;

class Activator
{
    public static function activate(): void
    {
        self::create_tables();
    }

    private static function create_tables(): void
    {
        $queries = [
            (new Supplier)->get_table_schema(),
            (new Warranty)->get_table_schema(),
            Batch::get_table_schema(),
        ];

        if (! function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $queries && dbDelta($queries);
    }
}
