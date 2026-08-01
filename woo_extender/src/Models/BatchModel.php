<?php

declare(strict_types=1);

namespace WooExtender\Models;

defined('ABSPATH') || exit;

class BatchModel extends BaseModel
{
    public static function get_form_fields(): array
    {
        return [
            'product_id' => [
                'label' => __('Product', 'woo-extender'),
                'type'  => 'number',
                'ui_type' => 'parent_product_search',
                'action' => 'woo_extender_search_parent_products',
                'required' => true
            ],
            'variation_id' => [
                'label' => __('Variation', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'variation_product_search',
                'action' => 'woo_extender_get_product_variations',
                'required' => false
            ],
            'supplier_id' => [
                'label' => __('Supplier', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'ajax_select',
                'action' => 'woo_extender_supplier_search',
                'required' => true
            ],
            'warranty_provider_id' => [
                'label' => __('Warranty Provider', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'ajax_select',
                'action' => 'woo_extender_warranty_search',
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
            'buy_price' => [
                'label' => __('Buy Price', 'woo-extender'),
                'type' => 'text',
                'data_type' => 'float',
                'required' => true
            ],
            'sell_price' => [
                'label' => __('Sell Price', 'woo-extender'),
                'type' => 'text',
                'data_type' => 'float',
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
}
