<?php

declare(strict_types=1);

namespace WooExtender\Repository;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;
use WooExtender\Models\WarrantyModel;

defined('ABSPATH') || exit;

class WarrantyRepository extends BaseRepository
{

    protected string $table_name = 'woo_extndr_warranty_providers';
    protected array $searchable_columns = ['name', 'slug'];
    protected array $allowed_orderby = ['name', 'slug', 'created_at'];

    protected function get_child_schema_fields(): string
    {
        $query =
            "name VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            support_phone VARCHAR(50) NULL,
            email VARCHAR(190) NULL,
            website VARCHAR(255) NULL,
            warranty_default_months INT NULL,
            notes TEXT NULL,";

        return $query;
    }

    protected function get_child_schema_indexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    protected function get_model_class(): string
    {
        return WarrantyModel::class;
    }

    protected function is_valid_for_save(BaseModel $model): bool
    {
        return ! empty($model->name);
    }

    protected function sanitize_child_fields(BaseModel $model): array
    {

        $prepared = [];

        if (isset($model->phone))                    $prepared['phone'] = Sanitize::string($model->phone);
        if (isset($model->support_phone))            $prepared['support_phone'] = Sanitize::string($model->support_phone);
        if (isset($model->email))                    $prepared['email'] = Sanitize::email($model->email);
        if (isset($model->website))                  $prepared['website'] = Sanitize::url($model->website);
        if (isset($model->warranty_default_months))  $prepared['warranty_default_months'] = Sanitize::int($model->warranty_default_months);
        if (isset($model->notes))                    $prepared['notes'] = Sanitize::textarea($model->notes);

        return $prepared;
    }
}
