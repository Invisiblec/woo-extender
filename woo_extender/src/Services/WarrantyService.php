<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\DTO\WarrantyData;
use WooExtender\Helpers\Sanitize;
use WooExtender\Models\Warranty;

defined('ABSPATH') || exit;

class WarrantyService
{
    public function get_table_schema(): string
    {
        return Warranty::get_table_schema();
    }

    public function save(WarrantyData $dto): int|bool
    {
        do_action('woo_extender_before_warranty_save', $dto);

        $id = isset($dto->id) ? Sanitize::int($dto->id) : 0;

        if ($id > 0) {
            $warranty = Warranty::get_by_id($id);
            if (! $warranty) return false;
        } else {
            $warranty = new Warranty();
        }

        foreach (get_object_vars($dto) as $key => $value) {
            if ($key !== 'id') {
                $warranty->$key = $value;
            }
        }

        $saved_id = $warranty->save();

        do_action('woo_extender_after_warranty_save', $saved_id, $warranty);

        return $saved_id;
    }

    public function delete(Warranty $warranty): bool
    {
        do_action('woo_extender_before_warranty_delete', $warranty);

        $result = $warranty->delete();

        if (! $result) return false;

        do_action('woo_extender_after_warranty_delete', $warranty);

        return true;
    }

    public function get_list(array $args = []): array
    {
        return Warranty::get_all($args);
    }

    public function get_by_id(int $id): ?Warranty
    {
        return Warranty::get_by_id($id);
    }

    public function get_as_options_list(): ?array
    {
        return Warranty::get_as_options_list();
    }

    public function get_form_fields(): array
    {
        return Warranty::get_form_fields();
    }

    public function get_count(array $args = []): string
    {
        return Warranty::get_count($args);
    }
}
