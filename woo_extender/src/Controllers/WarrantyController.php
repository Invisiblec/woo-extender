<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use Override;
use WooExtender\DTO\Factory\WarrantyDataFactory;
use WooExtender\Enums\Pages;
use WooExtender\Services\WarrantyService;

defined('ABSPATH') || exit;

/**
 * @extends BaseController<WarrantyService>
 */
class WarrantyController extends BaseController
{
    public function __construct(WarrantyService $service)
    {
        parent::__construct($service);
    }

    #[Override]
    protected function getPage(): Pages
    {
        return Pages::Warranties;
    }

    #[Override]
    protected function getNonceAction(): string
    {
        return 'save_warranty_action';
    }

    #[Override]
    protected function getNonceField(): string
    {
        return 'warranty_nonce_field';
    }

    #[Override]
    protected function getTableNonceAction(): string
    {
        return 'bulk-warranties';
    }

    #[Override]
    protected function getTableNonceField(): string
    {
        return '_wpnonce-warranties';
    }

    #[Override]
    protected function getFactoryClass(): string
    {
        return WarrantyDataFactory::class;
    }

    #[Override]
    protected function getClassPrefix(): string
    {
        return 'Warranty';
    }

    #[Override]
    protected static function getGlobalVar(): string
    {
        return 'warranty_table';
    }
}
