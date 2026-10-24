<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use Override;
use WooExtender\Admin\AdminNotices;
use WooExtender\DTO\Factory\SupplierDataFactory;
use WooExtender\Enums\Pages;
use WooExtender\Services\SupplierService;

defined('ABSPATH') || exit;

/**
 * @extends BaseController<SupplierService>
 */
class SupplierController extends BaseController
{
    public function __construct(SupplierService $service, AdminNotices $adminNotices)
    {
        parent::__construct($service, $adminNotices);
    }

    #[Override]
    protected function getPage(): Pages
    {
        return Pages::Suppliers;
    }

    #[Override]
    protected function getNonceAction(): string
    {
        return 'save_supplier_action';
    }

    #[Override]
    protected function getNonceField(): string
    {
        return 'supplier_nonce_field';
    }

    #[Override]
    protected function getTableNonceAction(): string
    {
        return 'bulk-suppliers';
    }

    #[Override]
    protected function getTableNonceField(): string
    {
        return '_wpnonce-suppliers';
    }

    #[Override]
    protected function getFactoryClass(): string
    {
        return SupplierDataFactory::class;
    }

    #[Override]
    protected function getClassPrefix(): string
    {
        return 'Supplier';
    }

    #[Override]
    protected static function getGlobalVar(): string
    {
        return 'supplier_table';
    }
}