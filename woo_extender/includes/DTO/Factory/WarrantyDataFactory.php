<?php

namespace WooExtender\DTO\Factory;

use WooExtender\DTO\WarrantyData;
use WooExtender\Validation\DataValidator;

defined('ABSPATH') || exit;

class WarrantyDataFactory
{
    public static function createDTO(array $raw_data, array $fields): WarrantyData
    {
        $data = DataValidator::validate($raw_data, $fields);

        return new WarrantyData(
            name: $data['name'],
            slug: $data['slug'],
            id: $data['id'],
            phone: $data['phone'],
            support_person: $data['support_person'],
            email: $data['email'],
            website: $data['website'],
            warranty_default_months: $data['warranty_default_months'],
            notes: $data['notes'],
        );
    }
}