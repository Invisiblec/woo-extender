<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\BatchData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\Batch;

defined('ABSPATH') || exit;

class BatchService
{
    private $cache = [];

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

    public function save(BatchData $dto): int|bool
    {
        do_action('woo_extender_before_batch_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $batch = Batch::get_by_id($id);
            if (! $batch) return false;
        } else {
            $batch = new Batch();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $batch->$key = $value;
            }
        }

        $saved_id = $batch->save();

        do_action('woo_extender_after_batch_save', $saved_id, $batch);

        return $saved_id;
    }

    public function delete(Batch $batch): bool
    {
        do_action('woo_extender_before_batch_delete', $batch);

        $result = $batch->delete();

        if (! $result) return false;

        do_action('woo_extender_after_batch_delete', $batch);

        return true;
    }
}
