<?php

declare(strict_types=1);

namespace WooExtender\Repositories;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;
use WooExtender\Models\SupplierModel;

defined('ABSPATH') || exit;

class SupplierRepository extends BaseRepository
{
    protected string $table_name = 'woo_extndr_suppliers';
    protected array $searchable_columns = ['name', 'slug'];
    protected array $allowed_orderby = ['name', 'slug', 'created_at'];

    protected function get_child_schema_fields(): string
    {
        return
            "name VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            sales_person VARCHAR(190) NULL,
            sales_phone VARCHAR(50) NULL,
            email VARCHAR(190) NULL,
            website VARCHAR(255) NULL,
            address TEXT NULL,
            notes TEXT NULL,";
    }

    protected function get_child_schema_indexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    protected function get_model_class(): string
    {
        return SupplierModel::class;
    }

    protected function is_valid_for_save(BaseModel $model): bool
    {
        return ! empty($model->name);
    }

    protected function sanitize_child_fields(BaseModel $model): array
    {
        $prepared = [];

        if (isset($model->phone))        $prepared['phone'] = Sanitize::string($model->phone);
        if (isset($model->sales_person)) $prepared['sales_person'] = Sanitize::string($model->sales_person);
        if (isset($model->sales_phone))  $prepared['sales_phone'] = Sanitize::string($model->sales_phone);
        if (isset($model->email))        $prepared['email'] = Sanitize::email($model->email);
        if (isset($model->website))      $prepared['website'] = Sanitize::url($model->website);
        if (isset($model->address))      $prepared['address'] = Sanitize::textarea($model->address);
        if (isset($model->notes))        $prepared['notes'] = Sanitize::textarea($model->notes);

        return $prepared;
    }
}