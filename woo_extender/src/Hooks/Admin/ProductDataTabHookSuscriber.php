<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Admin\ProductDataTabs\ProductDataTab;
use WooExtender\Interfaces\HookSubscriberInterface;

defined('ABSPATH') || exit;

class ProductDataTabHookSuscriber implements HookSubscriberInterface
{
    public function __construct(public ProductDataTab $productDataTab) {}

    #[Override]
    public function subscribe(): void
    {
        add_filter('woocommerce_product_data_tabs', [$this->productDataTab, 'register']);
        add_action('woocommerce_product_data_panels', [$this->productDataTab, 'render']);
        add_action('woocommerce_admin_process_product_object', [$this->productDataTab, 'save']);
        add_action('admin_enqueue_scripts', [$this->productDataTab, 'enqueue_admin_scripts']);
    }
}