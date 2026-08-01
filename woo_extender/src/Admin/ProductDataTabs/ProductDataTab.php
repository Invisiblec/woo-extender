<?php

declare(strict_types=1);

namespace WooExtender\Admin\ProductDataTabs;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class ProductDataTab
{
    public function __construct()
    {
        add_filter('woocommerce_product_data_tabs', [$this, 'register']);
        add_action('woocommerce_product_data_panels', [$this, 'render']);
        add_action('woocommerce_admin_process_product_object', [$this, 'save']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function register(array $tabs): array
    {
        $tabs['woo_extend'] = [
            'label' => __('Meta', 'woo-extender'),
            'target' => 'woo_extndr_product_data',
            'class' => ['show_if_simple', 'show_if_variable'],
            'priority' => 0,
        ];

        return $tabs;
    }

    public function render(): void
    {
        $view_path = WOO_EXTNDR_PATH . 'resources/views/admin/html-product-data-tab.php';

        if (! file_exists($view_path)) return;

        include $view_path;
    }

    public function save(\WC_Product $product): void
    {
        $eng_title = Sanitize::string(wp_unslash($_POST['woo_extndr_eng_title'])) ?? '';

        if (!empty($eng_title)) {
            $product->update_meta_data('_woo_extndr_english_title', $eng_title);
        } else {
            $product->delete_meta_data('_woo_extndr_english_title');
        }

        $main_features = $_POST['woo_extndr_product_main_feature'] ?? [];

        if (is_array($main_features)) {
            $main_features = Sanitize::field($main_features);
            $main_features = array_filter($main_features, function ($feature) {
                return ! empty($feature['title']) || ! empty($feature['value']);
            });
            $main_features = array_values($main_features);
        } else {
            $main_features = [];
        }

        if ($main_features) {
            $product->update_meta_data('_woo_extndr_product_main_features', $main_features);
        } else {
            $product->delete_meta_data('_woo_extndr_product_main_features');
        }
    }

    public function enqueue_admin_scripts(mixed $hook): void
    {
        if (! in_array($hook, ['post.php', 'post-new.php'], true)) return;

        global $post;
        if (! $post || 'product' !== $post->post_type) return;

        wp_enqueue_style(
            'woo-extndr-product-data-tab-style',
            WOO_EXTNDR_URL . 'assets/css/product-data-tab.css',
            [],
            '1.0.0',
        );

        wp_enqueue_script(
            'woo-extndr-product-data-tab-script',
            WOO_EXTNDR_URL . 'assets/js/product-data-tab.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
}
