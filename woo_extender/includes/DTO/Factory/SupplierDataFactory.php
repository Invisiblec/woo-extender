<?php

namespace WooExtender\DTO\Factory;

use WooExtender\DTO\SupplierData;
use WooExtender\Validation\DataValidator;

defined('ABSPATH') || exit;

class SupplierDataFactory
{
    public static function createDTO(array $raw_data, array $fields): SupplierData
    {
        $data = DataValidator::validate($raw_data, $fields);

        return new SupplierData(
            name: $data['name'],
            slug: $data['slug'],
            id: $data['id'],
            phone: $data['phone'],
            sales_person: $data['sales_person'],
            sales_phone: $data['sales_phone'],
            email: $data['email'],
            website: $data['website'],
            address: $data['address'],
            notes: $data['notes'],
        );
    }
}