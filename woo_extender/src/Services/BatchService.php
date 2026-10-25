<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\BatchData;
use WooExtender\DTO\Changeset;
use WooExtender\Exceptions\BusinessRuleException;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BatchModel;
use WooExtender\Repositories\BatchRepository;

defined('ABSPATH') || exit;

class BatchService
{
    private $cache = [];

    public function __construct(private BatchRepository $repo) {}

    public function getTableSchema(): string
    {
        return $this->repo->getTableSchema();
    }

    public function getList(array $args = []): array
    {
        return $this->repo->getAll($args);
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

    public function getCount(array $args = []): string
    {
        return $this->repo->getCount($args);
    }

    public function save(BatchData $dto): int|bool
    {
        do_action('woo_extender_before_batch_save', $dto);

        $batch = new BatchModel();

        foreach (get_object_vars($dto) as $key => $value) {
            $batch->$key = $value;
        }

        $saved_id = $this->repo->save($batch);

        if (! $saved_id) return false;

        do_action('woo_extender_after_batch_save', $batch);

        return $saved_id;
    }

    public function update(Changeset $changeset): int|bool
    {
        do_action('woo_extender_before_batch_update', $changeset);

        $id = isset($changeset->id) ? Sanitize::int($changeset->id) : 0;

        if ($id <= 0) return false;

        $batch = $this->repo->getById($id);

        if (! $batch) return false;

        $oldQty = $batch->quantity_total ?? 0;

        foreach ($changeset->changes as $key => $value) {
            $batch->$key = $value;
        }

        $update_id = $this->repo->save($batch);

        if (! $update_id) return false;

        do_action('woo_extender_after_batch_update', $batch, $oldQty);

        return $update_id;
    }

    /**
     * @throws BusinessRuleException When the batch has sales or reservations.
     */
    public function delete(BatchModel $batch): bool
    {
        if ((int) $batch->quantity_sold > 0 || (int) $batch->quantity_reserved > 0) {
            throw new BusinessRuleException(sprintf(
                __('Batch #%d has sales or reservations and cannot be deleted.', 'woo-extender'),
                $batch->id
            ));
        }

        do_action('woo_extender_before_batch_delete', $batch);

        $result = $this->repo->delete($batch);

        if (! $result) return false;

        do_action('woo_extender_after_batch_delete', $batch);

        return true;
    }
}