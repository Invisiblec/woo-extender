<?php

declare(strict_types=1);

namespace WooExtender\Models;

defined('ABSPATH') || exit;

class BatchModel extends BaseModel
{
    public static function getFormFields(): array
    {
        return [
            'product_id' => [
                'label' => __('Product', 'woo-extender'),
                'type'  => 'number',
                'ui_type' => 'parent_product_search',
                'action' => 'woo_extender_search_parent_products',
                'editable' => false,
                'required' => true
            ],
            'variation_id' => [
                'label' => __('Variation', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'variation_product_search',
                'action' => 'woo_extender_get_product_variations',
                'editable' => false,
                'required' => false
            ],
            'supplier_id' => [
                'label' => __('Supplier', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'ajax_select',
                'action' => 'woo_extender_supplier_search',
                'editable' => false,
                'required' => true
            ],
            'warranty_provider_id' => [
                'label' => __('Warranty Provider', 'woo-extender'),
                'type' => 'number',
                'ui_type' => 'ajax_select',
                'action' => 'woo_extender_warranty_search',
                'editable' => false,
                'required' => false
            ],
            'sku' => [
                'label' => __('SKU', 'woo-extender'),
                'type' => 'text',
                'editable' => true,
                'required' => false
            ],
            'quantity_total' => [
                'label' => __('Quantity Total', 'woo-extender'),
                'type' => 'number',
                'editable' => true,
                'required' => true
            ],
            'buy_price' => [
                'label' => __('Buy Price', 'woo-extender'),
                'type' => 'text',
                'data_type' => 'float',
                'editable' => true,
                'required' => true
            ],
            'sell_price' => [
                'label' => __('Sell Price', 'woo-extender'),
                'type' => 'text',
                'data_type' => 'float',
                'editable' => true,
                'required' => false
            ],
            'warranty_start_date' => [
                'label' => __('Warranty Start', 'woo-extender'),
                'type' => 'date',
                'editable' => true,
                'required' => false
            ],
            'warranty_end_date' => [
                'label' => __('Warranty End', 'woo-extender'),
                'type' => 'date',
                'editable' => true,
                'required' => false
            ],
            'purchase_date' => [
                'label' => __('Purchase Date', 'woo-extender'),
                'type' => 'date',
                'editable' => true,
                'required' => false
            ]
        ];
    }
}
