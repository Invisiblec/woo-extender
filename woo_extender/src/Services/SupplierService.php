<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\SupplierData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\SupplierModel;
use WooExtender\Repositories\SupplierRepository;

defined('ABSPATH') || exit;

class SupplierService
{
    public function __construct(private SupplierRepository $repo) {}

    public function get_table_schema(): string
    {
        return $this->repo->get_table_schema();
    }

    public function get_list(array $args = []): array
    {
        return $this->repo->get_all($args);
    }

    public function getById(int $id): ?SupplierModel
    {
        return $this->repo->getById($id);
    }

    public function getFormFields(): array
    {
        return SupplierModel::getFormFields();
    }

    public function get_count(array $args = []): string
    {
        return $this->repo->get_count($args);
    }

    public function get_as_options_list(): ?array
    {
        return $this->repo->get_as_options_list();
    }

    public function save(SupplierData $dto): int|bool
    {
        do_action('woo_extender_before_supplier_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $supplier = $this->repo->getById($id);
            if (! $supplier) return false;
        } else {
            $supplier = new SupplierModel();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $supplier->$key = $value;
            }
        }

        $saved_id = $this->repo->save($supplier);

        do_action('woo_extender_after_supplier_save', $saved_id, $supplier);

        return $saved_id;
    }

    public function delete(SupplierModel $supplier): bool
    {
        do_action('woo_extender_before_supplier_delete', $supplier);

        $result = $this->repo->delete($supplier);

        if (! $result) return false;

        do_action('woo_extender_after_supplier_delete', $supplier);

        return true;
    }
}
