<?php

namespace WooExtender\Services;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\Batch;

defined('ABSPATH') || exit;

class BatchServices
{
    private static $cache = [];

    public function get_table_schema(): string
    {
        return Batch::get_table_schema();
    }

    public function get_list(array $args = []): array
    {
        return Batch::get_all($args);
    }

    public function get_count(array $args = []): string
    {
        return Batch::get_count($args);
    }

    public function get_by_id(int $id): ?Batch
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = Batch::get_by_id($id);
        }
        return $this->cache[$id];
    }

    public function get_form_fields(): array
    {
        return Batch::get_form_fields();
    }

    public function save(object $dto): int|bool
    {
        do_action('woo_extender_before_batch_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $supplier = Batch::get_by_id($id);
            if (! $supplier) return false;
        } else {
            $supplier = new Batch();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $supplier->$key = $value;
            }
        }

        $saved_id = $supplier->save();

        do_action('woo_extender_after_batch_save', $saved_id, $supplier);

        return $saved_id;
    }

    public function delete(Batch $supplier): bool
    {
        do_action('woo_extender_before_supplier_delete', $supplier);

        $result = $supplier->delete();

        if (! $result) return false;

        do_action('woo_extender_after_supplier_delete', $supplier);

        return true;
    }
}