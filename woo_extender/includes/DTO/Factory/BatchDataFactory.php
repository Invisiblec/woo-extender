<?php

namespace WooExtender\DTO\Factory;

use DateTime;
use WooExtender\DTO\BatchData;

defined('ABSPATH') || exit;

class BatchDataFactory
{
    public static function createDTO(array $data, ?int $id = null): BatchData
    {

        $keys = [
            'warranty_start_date',
            'warranty_end_date',
            'purchase_date',
        ];

        $dates = array_map(fn($key) => !empty($data[$key]) ? new DateTime($data[$key]) : null, $keys);

        $dates = array_combine($keys, $dates);

        return new BatchData(
            product_id: $data['product_id'],
            supplier_id: $data['supplier_id'],
            quantity_total: $data['quantity_total'],
            quantity_reserved: $data['quantity_reserved'],
            quantity_sold: $data['quantity_sold'],
            buy_price: $data['buy_price'],
            id: $id,
            warranty_id: $data['warranty_id'] ?? null,
            sell_price: $data['sell_price'] ?? null,
            warranty_start_date: $dates['warranty_start_date'],
            warranty_end_date: $dates['warranty_end_date'],
            purchase_date: $dates['purchase_date'],
            variation_id: $data['variation_id'] ?? null,
            sku: $data['sku'] ?? null
        );
    }
}