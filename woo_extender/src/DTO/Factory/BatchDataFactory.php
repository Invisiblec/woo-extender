<?php

declare(strict_types=1);

namespace WooExtender\DTO\Factory;

use WooExtender\DTO\BatchData;
use WooExtender\Validation\DataValidator;

defined('ABSPATH') || exit;

class BatchDataFactory
{
    public static function createDTO(array $raw_data, array $fields): BatchData
    {

        $data = DataValidator::validate($raw_data, $fields);

        // $keys = [
        //     'warranty_start_date',
        //     'warranty_end_date',
        //     'purchase_date',
        // ];

        // $dates = array_map(fn($key) => !empty($data[$key]) ? new DateTimeImmutable($data[$key]) : null, $keys);

        // $dates = array_combine($keys, $dates);

        return new BatchData(
            product_id: $data['product_id'],
            supplier_id: $data['supplier_id'],
            quantity_total: $data['quantity_total'],
            buy_price: $data['buy_price'],
            id: $data['id'],
            warranty_id: $data['warranty_provider_id'] ?? null,
            sell_price: $data['sell_price'] ?? null,
            warranty_start_date: $data['warranty_start_date'],
            warranty_end_date: $data['warranty_end_date'],
            purchase_date: $data['purchase_date'],
            variation_id: $data['variation_id'] ?? null,
            sku: $data['sku'] ?? null
        );
    }
}
