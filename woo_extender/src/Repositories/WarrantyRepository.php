<?php

declare(strict_types=1);

namespace WooExtender\Repositories;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;
use WooExtender\Models\WarrantyModel;

defined('ABSPATH') || exit;

class WarrantyRepository extends BaseRepository
{

    protected string $tableName = 'woo_extndr_warranty_providers';
    protected array $searchableColumns = ['name', 'slug'];
    protected array $allowedOrderby = ['name', 'slug', 'created_at'];

    protected function getChildSchemaFields(): string
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

    protected function getChildSchemaIndexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    protected function getModelClass(): string
    {
        return WarrantyModel::class;
    }

    protected function isValidForSave(BaseModel $model): bool
    {
        return ! empty($model->name);
    }

    protected function sanitizeChildFields(BaseModel $model): array
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
