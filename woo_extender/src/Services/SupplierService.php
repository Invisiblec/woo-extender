<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\Changeset;
use WooExtender\DTO\SupplierData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\SupplierModel;
use WooExtender\Repositories\SupplierRepository;

defined('ABSPATH') || exit;

class SupplierService
{
    public function __construct(private SupplierRepository $repo) {}

    public function getTableSchema(): string
    {
        return $this->repo->getTableSchema();
    }

    public function getList(array $args = []): array
    {
        return $this->repo->getAll($args);
    }

    public function getById(int $id): ?SupplierModel
    {
        return $this->repo->getById($id);
    }

    public function getFormFields(): array
    {
        return SupplierModel::getFormFields();
    }

    public function getCount(array $args = []): string
    {
        return $this->repo->getCount($args);
    }

    public function getAsOptionsList(): ?array
    {
        return $this->repo->getAsOptionsList();
    }

    public function save(SupplierData $dto): int|bool
    {
        do_action('woo_extender_before_supplier_save', $dto);

        $supplier = new SupplierModel();

        foreach (get_object_vars($dto) as $key => $value) {
            $supplier->$key = $value;
        }

        $saved_id = $this->repo->save($supplier);

        do_action('woo_extender_after_supplier_save', $saved_id, $supplier);

        return $saved_id;
    }

    public function update(Changeset $changeset): int|bool
    {
        do_action('woo_extender_before_supplier_update', $changeset);

        $id = isset($changeset->id) ? Sanitize::int($changeset->id) : 0;

        if ($id <= 0) return false;

        $supplier = $this->repo->getById($id);

        if (! $supplier) return false;

        foreach ($changeset->changes as $key => $value) {
            $supplier->$key = $value;
        }

        $update_id = $this->repo->save($supplier);

        if (! $update_id) return false;

        do_action('woo_extender_after_supplier_update', $supplier);

        return $update_id;
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