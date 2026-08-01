<?php

declare(strict_types=1);

namespace WooExtender\Services;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class InventoryService
{
    public function __construct()
    {
        add_action('woo_extender_after_batch_save', [$this, 'sync_product_stock'], 10, 2);
    }

    public function sync_product_stock(int $batch_id, object $batch): void
    {
        $product_id = $batch->product_id;
        $variation_id = $batch->variation_id;
        $batch_qty = $batch->quantity_total;

        $target_id = ! empty($variation_id) ? Sanitize::int($variation_id) : Sanitize::int($product_id);

        if ($target_id <= 0 || $batch_qty <= 0) return;

        if (get_post_meta($target_id, '_manage_stock', true) !== 'yes') {
            $product = wc_get_product($target_id);
            if ($product) {
                $product->set_manage_stock('yes');
                $product->set_stock_quantity($batch_qty);
                $product->save();

                if (! empty($variation_id)) {
                    wc_delete_product_transients($product_id);
                }
                return;
            }
        }

        wc_update_product_stock($target_id, $batch_qty, 'increase');

        if (! empty($variation_id)) wc_delete_product_transients($product_id);
    }
}