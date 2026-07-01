<?php

namespace WooExtender\Admin\Controller;

use Override;
use WooExtender\Enums\Pages;
use WooExtender\Services\SupplierServices as Supplier;

defined('ABSPATH') || exit;

class SupplierController extends BaseController
{
    #[Override]
    protected function get_page(): Pages
    {
        return Pages::Suppliers;
    }

    #[Override]
    protected function get_nonce_action(): string
    {
        return 'save_supplier_action';
    }

    #[Override]
    protected function get_nonce_field(): string
    {
        return 'supplier_nonce_field';
    }

    #[Override]
    protected function get_table_nonce_action(): string
    {
        return 'bulk-suppliers';
    }

    #[Override]
    protected function get_table_nonce_field(): string
    {
        return '_wpnonce-suppliers';
    }

    #[Override]
    protected function get_service_class(): string
    {
        return Supplier::class;
    }

    #[Override]
    protected function get_class_prefix(): string
    {
        return 'Supplier';
    }

    #[Override]
    protected static function get_global_var(): string
    {
        return 'supplier_table';
    }
}
