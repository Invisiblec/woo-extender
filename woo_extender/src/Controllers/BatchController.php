<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use Override;
use WooExtender\DTO\Factory\BatchDataFactory;
use WooExtender\Enums\Pages;
use WooExtender\Helpers\Sanitize;
use WooExtender\Services\BatchService;
use WooExtender\Services\WarrantyService;
use WooExtender\Validation\DataValidator;

defined('ABSPATH') || exit;
/**
 * @extends BaseController<BatchService>
 */
class BatchController extends BaseController
{
    public function __construct(BatchService $service, protected WarrantyService $dependedService)
    {
        parent::__construct($service);
    }

    #[Override]
    protected function getPage(): Pages
    {
        return Pages::Batches;
    }

    #[Override]
    protected function getNonceAction(): string
    {
        return 'save_batch_action';
    }

    #[Override]
    protected function getNonceField(): string
    {
        return 'batch_nonce_field';
    }

    #[Override]
    protected function getTableNonceAction(): string
    {
        return 'bulk-batches';
    }

    #[Override]
    protected function getTableNonceField(): string
    {
        return '_wpnonce-batches';
    }

    #[Override]
    protected function getFactoryClass(): string
    {
        return BatchDataFactory::class;
    }

    #[Override]
    protected function getClassPrefix(): string
    {
        return 'Batch';
    }

    #[Override]
    protected static function getGlobalVar(): string
    {
        return 'batch_table';
    }

    #[Override]
    protected function getRenderData(object $item): array
    {
        $data = [];
        $warranty = $this->dependedService->getById(Sanitize::int($item->warranty_provider_id));
        $data['warranty_provider_name'] = $warranty ? $warranty->name : '';
        return $data;
    }
}