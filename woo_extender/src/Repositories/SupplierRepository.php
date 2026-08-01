<?php

declare(strict_types=1);

namespace WooExtender\Repositories;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;
use WooExtender\Models\SupplierModel;

defined('ABSPATH') || exit;

class SupplierRepository extends BaseRepository
{
    protected string $tableName = 'woo_extndr_suppliers';
    protected array $searchableColumns = ['name', 'slug'];
    protected array $allowedOrderby = ['name', 'slug', 'created_at'];

    protected function getChildSchemaFields(): string
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

    protected function getChildSchemaIndexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    protected function getModelClass(): string
    {
        return SupplierModel::class;
    }

    protected function isValidForSave(BaseModel $model): bool
    {
        return ! empty($model->name);
    }

    protected function sanitizeChildFields(BaseModel $model): array
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
