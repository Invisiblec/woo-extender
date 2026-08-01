<?php

declare(strict_types=1);

use WooExtender\Core\WooExtender;
use WooExtender\Models\BatchModel;
use WooExtender\Models\SupplierModel;
use WooExtender\Models\WarrantyModel;
use WooExtender\Services\BatchService;
use WooExtender\Services\SupplierService;
use WooExtender\Services\WarrantyService;

function woo_extender_get_supplier(int $supplier_id): ?SupplierModel
{
    return WooExtender::make(SupplierService::class)->getById($supplier_id);
}

function woo_extender_get_supplier_name(int $supplier_id): ?string
{
    return woo_extender_get_supplier($supplier_id)?->name;
}

function woo_extender_get_warranty(int $warranty_id): ?WarrantyModel
{
    return WooExtender::make(WarrantyService::class)->getById($warranty_id);
}

function woo_extender_get_warranty_name(int $warranty_id): ?string
{
    return woo_extender_get_warranty($warranty_id)?->name;
}

function woo_extender_get_batch(int $batch_id): ?BatchModel
{
    return WooExtender::make(BatchService::class)->getById($batch_id);
}

function woo_extender_get_batch_list(array $args): array
{
    return WooExtender::make(BatchService::class)->getList($args);
}

function woo_extender_get_batch_count(array $args): string
{
    return WooExtender::make(BatchService::class)->getCount($args);
}
