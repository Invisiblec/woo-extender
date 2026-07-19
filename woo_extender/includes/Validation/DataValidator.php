<?php

namespace WooExtender\Validation;

use WooExtender\Exceptions\ValidationException;
use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class DataValidator
{
    public static function validate(array $input_data, array $fields): array
    {
        $clean_data = [];
        $errors = [];

        $clean_data['id'] = isset($input_data['id']) ? Sanitize::int($input_data['id']) : null;

        foreach ($fields as $field_key => $field_meta) {
            $raw_value = $input_data[$field_key] ?? '';
            $data_type = $field_meta['data_type'] ?? $field_meta['type'];
            $clean_value = Sanitize::field($raw_value, $data_type);

            if ($field_meta['required'] && empty($clean_value)) {
                $errors[$field_key] = sprintf(
                    __('The "%s" field is required and cannot be empty.', 'woo-extender'),
                    esc_html($field_meta['label'])
                );
                continue;
            }

            $clean_data[$field_key] = $clean_value;
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $clean_data;
    }
}