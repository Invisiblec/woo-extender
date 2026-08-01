<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use Override;
use WooExtender\DTO\Factory\WarrantyDataFactory;
use WooExtender\Enums\Pages;
use WooExtender\Services\WarrantyService as Warranty;

defined('ABSPATH') || exit;

class WarrantyController extends BaseController
{
    #[Override]
    protected function get_page(): Pages
    {
        return Pages::Warranties;
    }

    #[Override]
    protected function get_nonce_action(): string
    {
        return 'save_warranty_action';
    }

    #[Override]
    protected function get_nonce_field(): string
    {
        return 'warranty_nonce_field';
    }

    #[Override]
    protected function get_table_nonce_action(): string
    {
        return 'bulk-warranties';
    }

    #[Override]
    protected function get_table_nonce_field(): string
    {
        return '_wpnonce-warranties';
    }

    #[Override]
    protected function get_service_class(): string
    {
        return Warranty::class;
    }

    #[Override]
    protected function get_factory_class(): string
    {
        return WarrantyDataFactory::class;
    }

    #[Override]
    protected function get_class_prefix(): string
    {
        return 'Warranty';
    }

    #[Override]
    protected static function get_global_var(): string
    {
        return 'warranty_table';
    }
}
