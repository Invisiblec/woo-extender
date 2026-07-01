<?php

namespace WooExtender\Services;

use WooExtender\Models\Batch;

defined('ABSPATH') || exit;

class BatchServices
{

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
        return Batch::get_by_id($id);
    }
}
