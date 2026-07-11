<?php

use WooExtender\Models\Supplier;
use WooExtender\Services\SupplierServices;

function woo_extender_supplier_service(): SupplierServices
{
    static $service = null;
    if ($service === null) {
        $service = new SupplierServices();
    }
    return $service;
}

function woo_extender_get_supplier(int $supplier_id): ?Supplier
{
    return woo_extender_supplier_service()->get_by_id($supplier_id);
}

function woo_extender_get_supplier_name(int $supplier_id): ?string
{
    return woo_extender_get_supplier($supplier_id)->name;
}