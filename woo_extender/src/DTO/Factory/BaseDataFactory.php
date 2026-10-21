<?php

declare(strict_types=1);

namespace WooExtender\DTO\Factory;

use WooExtender\DTO\BaseData;
use WooExtender\DTO\Changeset;
use WooExtender\Helpers\Sanitize;
use WooExtender\Validation\DataValidator;

defined('ABSPATH') || exit;

abstract class BaseDataFactory
{
    abstract public static function createDTO(array $raw_data, array $fields): BaseData;

    public static function createChangeset(array $raw_data, array $fields): Changeset
    {
        $id = isset($raw_data['id']) ? Sanitize::int($raw_data['id']) : 0;
        $changedData = [];
        $validationFields = [];

        foreach ($fields as $field_key => $field_meta) {
            if (($field_meta['editable'] ?? true) && array_key_exists($field_key, $raw_data)) {
                $changedData[$field_key] = $raw_data[$field_key];
                $validationFields[$field_key] = $field_meta;
            }
        }

        $cleanData = DataValidator::validate($changedData, $validationFields);

        return new Changeset(
            $id,
            $cleanData
        );
    }
}