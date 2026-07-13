<?php

namespace WooExtender\Services;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\Supplier;

defined('ABSPATH') || exit;

class SupplierService
{
    public function get_table_schema(): string
    {
        return Supplier::get_table_schema();
    }

    public function save(object $dto): int|bool
    {
        do_action('woo_extender_before_supplier_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $supplier = Supplier::get_by_id($id);
            if (! $supplier) return false;
        } else {
            $supplier = new Supplier();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $supplier->$key = $value;
            }
        }

        $saved_id = $supplier->save();

        do_action('woo_extender_after_supplier_save', $saved_id, $supplier);

        return $saved_id;
    }

    public function delete(Supplier $supplier): bool
    {
        do_action('woo_extender_before_supplier_delete', $supplier);

        $result = $supplier->delete();

        if (! $result) return false;

        do_action('woo_extender_after_supplier_delete', $supplier);

        return true;
    }

    public function get_list(array $args = []): array
    {
        return Supplier::get_all($args);
    }

    public function get_by_id(int $id): ?Supplier
    {
        return Supplier::get_by_id($id);
    }

    public function get_as_options_list(): ?array
    {
        return Supplier::get_as_options_list();
    }

    public function get_form_fields(): array
    {
        return Supplier::get_form_fields();
    }

    public function get_count(array $args = []): string
    {
        return Supplier::get_count($args);
    }
}
