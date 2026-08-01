<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use WooExtender\Enums\Pages;
use WooExtender\Models\Supplier;
use WooExtender\Services\WarrantyService;
use WooExtender\Services\AccessController;

defined('ABSPATH') || exit;

class AjaxController
{
    public function __construct()
    {
        add_action('wp_ajax_woo_extender_search_parent_products', [$this, 'search_parent_products']);
        add_action('wp_ajax_woo_extender_get_product_variations', [$this, 'get_product_variations']);

        // Register supplier/warranty search actions expected by the form/model
        add_action('wp_ajax_woo_extender_supplier_search', [$this, 'search_suppliers']);
        add_action('wp_ajax_woo_extender_warranty_search', [$this, 'search_warranties']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function search_parent_products(): void
    {
        if (!check_ajax_referer('save_batch_action', 'security', false)) {
            wp_send_json_error(__('Security check failed.', 'woo-extender'), 403);
        }

        if (!AccessController::can_current_user_access(Pages::Batches)) {
            wp_send_json_error(__('Unauthorized access.', 'woo-extender'), 403);
        }

        $search_term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

        $args = [
            'status' => 'publish',
            'limit'  => 20,
            's'      => $search_term,
            'type'   => ['simple', 'variable'],
        ];

        $products = wc_get_products($args);
        $results  = [];

        foreach ($products as $product) {
            $results[] = [
                'id'   => $product->get_id(),
                'text' => $product->get_name()
            ];
        }

        wp_send_json($results);
    }

    public function get_product_variations(): void
    {
        if (!check_ajax_referer('save_batch_action', 'security', false) || !AccessController::can_current_user_access(Pages::Batches)) {
            wp_send_json_error(__('Invalid request.', 'woo-extender'), 403);
        }

        $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error(__('Invalid Product ID', 'woo-extender'));
        }

        $product = wc_get_product($product_id);
        if (!$product || !$product->is_type('variable')) {
            wp_send_json_success([]);
        }

        $variation_ids = $product->get_children();
        $results       = [];

        if (empty($variation_ids)) {
            wp_send_json_success([]);
        }

        foreach ($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);
            if (!$variation) {
                continue;
            }

            $attributes_label = [];
            foreach ($variation->get_attributes() as $taxonomy => $slug) {
                $term = get_term_by('slug', $slug, $taxonomy);
                $attributes_label[] = $term ? $term->name : $slug;
            }

            $results[] = [
                'id'   => $variation->get_id(),
                'text' => implode(' - ', $attributes_label),
            ];
        }

        wp_send_json_success($results);
    }

    public function search_suppliers(): void
    {
        if (!check_ajax_referer('save_batch_action', 'security', false) || !current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Invalid request.', 'woo-extender'), 403);
        }

        $search_term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $suppliers = Supplier::get_all(['search' => $search_term, 'limit' => 20]);
        $results = [];

        foreach ($suppliers as $supplier) {
            $results[] = [
                'id' => $supplier->id,
                'text' => $supplier->name
            ];
        }

        wp_send_json($results);
    }

    public function search_warranties(): void
    {
        if (!check_ajax_referer('save_batch_action', 'security', false) || !current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Invalid request.', 'woo-extender'), 403);
        }

        $search_term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

        $warranties = (new WarrantyService)->get_list(['search' => $search_term, 'limit' => 20]);
        $results = [];

        foreach ($warranties as $w) {
            $results[] = [
                'id' => $w->id,
                'text' => $w->name
            ];
        }

        wp_send_json($results);
    }

    public function enqueue_admin_assets(mixed $hook): void
    {
        if (strpos($hook, 'woo-extender-batches') === false) {
            return;
        }

        wp_enqueue_style('woocommerce_admin_styles');

        wp_enqueue_script(
            'woo-extender-admin-batch-js',
            WOO_EXTNDR_URL . 'assets/js/admin-batch.js',
            [
                'jquery',
                'selectWoo'
            ],
            '1.0.0',
            true
        );

        wp_localize_script('woo-extender-admin-batch-js', 'woo_extender_admin_batches', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('save_batch_action')
        ]);
    }
}
