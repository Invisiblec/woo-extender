<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\WarrantyData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\WarrantyModel;
use WooExtender\Repositories\WarrantyRepository;

defined('ABSPATH') || exit;

class WarrantyService
{
    public function __construct(private WarrantyRepository $repo) {}

    public function getTableSchema(): string
    {
        return $this->repo->getTableSchema();
    }

    public function getList(array $args = []): array
    {
        return $this->repo->getAll($args);
    }

    public function getById(int $id): ?WarrantyModel
    {
        return $this->repo->getById($id);
    }

    public function getFormFields(): array
    {
        return WarrantyModel::getFormFields();
    }

    public function getCount(array $args = []): string
    {
        return $this->repo->getCount($args);
    }

    public function getAsOptionsList(): ?array
    {
        return $this->repo->getAsOptionsList();
    }

    public function save(WarrantyData $dto): int|bool
    {
        do_action('woo_extender_before_warranty_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $warranty = $this->repo->getById($id);
            if (! $warranty) return false;
        } else {
            $warranty = new WarrantyModel();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $warranty->$key = $value;
            }
        }

        $saved_id = $this->repo->save($warranty);

        do_action('woo_extender_after_warranty_save', $saved_id, $warranty);

        return $saved_id;
    }

    public function delete(WarrantyModel $warranty): bool
    {
        do_action('woo_extender_before_warranty_delete', $warranty);

        $result = $this->repo->delete($warranty);

        if (! $result) return false;

        do_action('woo_extender_after_warranty_delete', $warranty);

        return true;
    }
}
