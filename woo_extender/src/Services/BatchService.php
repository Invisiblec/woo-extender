<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\BatchData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BatchModel;
use WooExtender\Repositories\BatchRepository;

defined('ABSPATH') || exit;

class BatchService
{
    private $cache = [];

    public function __construct(private BatchRepository $repo) {}

    public function get_table_schema(): string
    {
        return $this->repo->get_table_schema();
    }

    public function get_list(array $args = []): array
    {
        return $this->repo->get_all($args);
    }

    public function getById(int $id): ?BatchModel
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->repo->getById($id);
        }
        return $this->cache[$id];
    }

    public function getFormFields(): array
    {
        return BatchModel::getFormFields();
    }

    public function get_count(array $args = []): string
    {
        return $this->repo->get_count($args);
    }

    public function save(BatchData $dto): int|bool
    {
        do_action('woo_extender_before_batch_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $batch = $this->repo->getById($id);
            if (! $batch) return false;
        } else {
            $batch = new BatchModel();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $batch->$key = $value;
            }
        }

        $saved_id = $this->repo->save($batch);

        do_action('woo_extender_after_batch_save', $saved_id, $batch);

        return $saved_id;
    }

    public function delete(BatchModel $batch): bool
    {
        do_action('woo_extender_before_batch_delete', $batch);

        $result = $this->repo->delete($batch);

        if (! $result) return false;

        do_action('woo_extender_after_batch_delete', $batch);

        return true;
    }
}
