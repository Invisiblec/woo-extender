<?php

namespace WooExtender\Services;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\WarrantyProvider;

defined('ABSPATH') || exit;

class WarrantyService
{
    public function get_table_schema(): string
    {
        return WarrantyProvider::get_table_schema();
    }

    public function save(object $dto): int|bool
    {
        do_action('woo_extender_before_warranty_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $warranty = WarrantyProvider::get_by_id($id);
            if (! $warranty) return false;
        } else {
            $warranty = new WarrantyProvider();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $warranty->$key = $value;
            }
        }

        $saved_id = $warranty->save();

        do_action('woo_extender_after_warranty_save', $saved_id, $warranty);

        return $saved_id;
    }

    public function delete(WarrantyProvider $warranty): bool
    {
        do_action('woo_extender_before_warranty_delete', $warranty);

        $result = $warranty->delete();

        if (! $result) return false;

        do_action('woo_extender_after_warranty_delete', $warranty);

        return true;
    }

    public function get_list(array $args = []): array
    {
        return WarrantyProvider::get_all($args);
    }

    public function get_by_id(int $id): ?WarrantyProvider
    {
        return WarrantyProvider::get_by_id($id);
    }

    public function get_as_options_list(): ?array
    {
        return WarrantyProvider::get_as_options_list();
    }

    public function get_form_fields(): array
    {
        return WarrantyProvider::get_form_fields();
    }

    public function get_count(array $args = []): string
    {
        return WarrantyProvider::get_count($args);
    }
}