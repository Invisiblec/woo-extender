<?php

declare(strict_types=1);

namespace WooExtender\Repository;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;
use WooExtender\Models\BatchModel;

defined('ABSPATH') || exit;

class BatchRepository extends BaseRepository
{

    protected string $table_name = 'woo_extndr_batches';
    protected array $searchable_columns = ['sku'];
    protected string $display_column = 'sku';
    protected array $allowed_orderby = ['purchase_date', 'buy_price', 'quantity_total'];

    protected function get_child_schema_fields(): string
    {
        return
            "product_id BIGINT UNSIGNED NOT NULL,
            variation_id BIGINT UNSIGNED NULL,
            supplier_id BIGINT UNSIGNED NOT NULL,
            warranty_provider_id BIGINT UNSIGNED NULL,
            sku VARCHAR(100) NULL,
            quantity_total INT UNSIGNED NOT NULL DEFAULT 0,
            quantity_reserved INT UNSIGNED NOT NULL DEFAULT 0,
            quantity_sold INT UNSIGNED NOT NULL DEFAULT 0,
            quantity_available INT GENERATED ALWAYS AS (CONVERT(quantity_total, SIGNED) - CONVERT(quantity_reserved, SIGNED) - CONVERT(quantity_sold, SIGNED)) STORED,
            buy_price DECIMAL(15,2) NOT NULL DEFAULT 0,
            sell_price DECIMAL(15,2) NULL,
            warranty_start_date DATE NULL,
            warranty_end_date DATE NULL,
            purchase_date DATE NULL,
            cost_total DECIMAL(15,2) GENERATED ALWAYS AS (quantity_total * buy_price) STORED";
    }

    protected function get_child_schema_indexes(): string
    {
        return
            "KEY product_variation (product_id, variation_id),
            KEY supplier_idx (supplier_id),
            KEY warranty_provider_idx (warranty_provider_id),
            KEY fifo_lifo_idx (created_at, status)
            ";
    }

    protected function get_model_class(): string
    {
        return BatchModel::class;
    }

    protected function is_valid_for_save(BaseModel $model): bool
    {
        return ! empty($model->product_id) && ! empty($model->supplier_id);
    }

    protected function sanitize_child_fields(BaseModel $model): array
    {

        $prepared = [];

        if (isset($model->product_id))               $prepared['product_id'] = Sanitize::int($model->product_id);
        if (isset($model->variation_id))             $prepared['variation_id'] = Sanitize::int($model->variation_id);
        if (isset($model->supplier_id))              $prepared['supplier_id'] = Sanitize::int($model->supplier_id);
        if (isset($model->warranty_provider_id))     $prepared['warranty_provider_id'] = Sanitize::int($model->warranty_provider_id);
        if (isset($model->sku))                      $prepared['sku'] = Sanitize::string($model->sku);
        if (isset($model->quantity_total))           $prepared['quantity_total'] = Sanitize::int($model->quantity_total);
        if (isset($model->quantity_reserved))        $prepared['quantity_reserved'] = Sanitize::int($model->quantity_reserved);
        if (isset($model->quantity_sold))            $prepared['quantity_sold'] = Sanitize::int($model->quantity_sold);
        if (isset($model->buy_price))                $prepared['buy_price'] = Sanitize::float($model->buy_price);
        if (isset($model->sell_price))               $prepared['sell_price'] = Sanitize::float($model->sell_price);
        if (isset($model->purchase_date))            $prepared['purchase_date'] = Sanitize::date($model->purchase_date);
        if (isset($model->warranty_start_date))      $prepared['warranty_start_date'] = Sanitize::date($model->warranty_start_date);
        if (isset($model->warranty_end_date))        $prepared['warranty_end_date'] = Sanitize::date($model->warranty_end_date);

        return $prepared;
    }
}