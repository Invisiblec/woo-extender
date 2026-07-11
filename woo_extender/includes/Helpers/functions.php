<?php

use WooExtender\Core\WooExtender;
use WooExtender\Models\Batch;
use WooExtender\Models\Supplier;
use WooExtender\Models\WarrantyProvider;
use WooExtender\Services\BatchServices;
use WooExtender\Services\SupplierServices;
use WooExtender\Services\WarrantyServices;

function woo_extender_get_supplier(int $supplier_id): ?Supplier
{
    return WooExtender::service('supplier', SupplierServices::class)->get_by_id($supplier_id);
}

function woo_extender_get_supplier_name(int $supplier_id): ?string
{
    return woo_extender_get_supplier($supplier_id)?->name;
}

function woo_extender_get_warranty(int $warranty_id): ?WarrantyProvider
{
    return WooExtender::service('warranty', WarrantyServices::class)->get_by_id($warranty_id);
}

function woo_extender_get_warranty_name(int $warranty_id): ?string
{
    return woo_extender_get_warranty($warranty_id)?->name;
}

function woo_extender_get_batch(int $batch_id): ?Batch
{
    return WooExtender::service('batch', BatchServices::class)->get_by_id($batch_id);
}

function woo_extender_get_batch_list(array $args): array
{
    return WooExtender::service('batch', BatchServices::class)->get_list($args);
}

function woo_extender_get_batch_count(array $args): string
{
    return WooExtender::service('batch', BatchServices::class)->get_count($args);
}