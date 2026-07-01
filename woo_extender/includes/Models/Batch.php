<?php

namespace WooExtender\Models;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class Batch extends BaseModel
{

    protected static string $table_name = 'woo_extndr_batches';
    protected static array $searchable_columns = ['sku'];
    protected static string $display_column = 'sku';
    protected static array $allowed_orderby = ['purchase_date', 'buy_price', 'quantity_total'];

    protected static function get_child_schema_fields(): string
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

    protected static function get_child_schema_indexes(): string
    {
        return
            "KEY product_variation (product_id, variation_id),
            KEY supplier_idx (supplier_id),
            KEY warranty_provider_idx (warranty_provider_id),
            KEY fifo_lifo_idx (created_at, status)
            ";
    }

    public static function get_form_fields(): array
    {
        return [
            'product_id' => [
                'label' => __('Product ID', 'woo-extender'),
                'type'  => 'number',
                'required' => true
            ],
            'variation_id' => [
                'label' => __('Variation ID', 'woo-extender'),
                'type' => 'number',
                'required' => false
            ],
            'supplier_id' => [
                'label' => __('Supplier ID', 'woo-extender'),
                'type' => 'number',
                'required' => true
            ],
            'warranty_provider_id' => [
                'label' => __('Warranty Provider ID', 'woo-extender'),
                'type' => 'number',
                'required' => false
            ],
            'sku' => [
                'label' => __('SKU', 'woo-extender'),
                'type' => 'text',
                'required' => false
            ],
            'quantity_total' => [
                'label' => __('Quantity Total', 'woo-extender'),
                'type' => 'number',
                'required' => true
            ],
            'quantity_reserved' => [
                'label' => __('Quantity Reserved', 'woo-extender'),
                'type' => 'number',
                'required' => true
            ],
            'quantity_sold' => [
                'label' => __('Quantity Sold', 'woo-extender'),
                'type' => 'number',
                'required' => true
            ],
            'buy_price' => [
                'label' => __('Buy Price', 'woo-extender'),
                'type' => 'text',
                'required' => true
            ],
            'sell_price' => [
                'label' => __('Sell Price', 'woo-extender'),
                'type' => 'text',
                'required' => false
            ],
            'warranty_start_date' => [
                'label' => __('Warranty Start', 'woo-extender'),
                'type' => 'date',
                'required' => false
            ],
            'warranty_end_date' => [
                'label' => __('Warranty End', 'woo-extender'),
                'type' => 'date',
                'required' => false
            ],
            'purchase_date' => [
                'label' => __('Purchase Date', 'woo-extender'),
                'type' => 'date',
                'required' => false
            ]
        ];
    }

    protected function is_valid_for_save(): bool
    {
        return ! empty($this->product_id) && ! empty($this->supplier_id);
    }

    protected function sanitize_child_fields(): array
    {

        $prepared = [];

        if (isset($this->product_id))               $prepared['product_id'] = Sanitize::int($this->product_id);
        if (isset($this->variation_id))             $prepared['variation_id'] = Sanitize::int($this->variation_id);
        if (isset($this->supplier_id))              $prepared['supplier_id'] = Sanitize::int($this->supplier_id);
        if (isset($this->warranty_provider_id))     $prepared['warranty_provider_id'] = Sanitize::int($this->warranty_provider_id);
        if (isset($this->sku))                      $prepared['sku'] = Sanitize::string($this->sku);
        if (isset($this->quantity_total))           $prepared['quantity_total'] = Sanitize::int($this->quantity_total);
        if (isset($this->quantity_reserved))        $prepared['quantity_reserved'] = Sanitize::int($this->quantity_reserved);
        if (isset($this->quantity_sold))            $prepared['quantity_sold'] = Sanitize::int($this->quantity_sold);
        if (isset($this->buy_price))                $prepared['buy_price'] = Sanitize::float($this->buy_price);
        if (isset($this->sell_price))               $prepared['sell_price'] = Sanitize::float($this->sell_price);

        if (! empty($this->purchase_date)) {
            $prepared['purchase_date'] = date("Y-m-d", strtotime($this->purchase_date));
        } else {
            $prepared['purchase_date'] = null;
        }

        if (! empty($this->warranty_start_date)) {
            $prepared['warranty_start_date'] = date("Y-m-d", strtotime($this->warranty_start_date));
        } else {
            $prepared['warranty_start_date'] = null;
        }

        if (! empty($this->warranty_end_date)) {
            $prepared['warranty_end_date'] = date("Y-m-d", strtotime($this->warranty_end_date));
        } else {
            $prepared['warranty_end_date'] = null;
        }

        return $prepared;
    }
}
