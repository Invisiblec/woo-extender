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

    public function get_table_schema(): string
    {
        return $this->repo->get_table_schema();
    }

    public function get_list(array $args = []): array
    {
        return $this->repo->get_all($args);
    }

    public function getById(int $id): ?WarrantyModel
    {
        return $this->repo->getById($id);
    }

    public function getFormFields(): array
    {
        return WarrantyModel::getFormFields();
    }

    public function get_count(array $args = []): string
    {
        return $this->repo->get_count($args);
    }

    public function get_as_options_list(): ?array
    {
        return $this->repo->get_as_options_list();
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
