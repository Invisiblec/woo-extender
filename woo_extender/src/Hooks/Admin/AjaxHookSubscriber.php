<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Controllers\AjaxController;
use WooExtender\Interfaces\HookSubscriberInterface;

defined('ABSPATH') || exit;

class AjaxHookSubscriber implements HookSubscriberInterface
{
    public function __construct(public AjaxController $ajaxController) {}

    #[Override]
    public function subscribe(): void
    {
        add_action('wp_ajax_woo_extender_search_parent_products', [$this->ajaxController, 'searchParentProducts']);
        add_action('wp_ajax_woo_extender_get_product_variations', [$this->ajaxController, 'getProductVariations']);

        // Register supplier/warranty search actions expected by the form/model
        add_action('wp_ajax_woo_extender_supplier_search', [$this->ajaxController, 'searchSuppliers']);
        add_action('wp_ajax_woo_extender_warranty_search', [$this->ajaxController, 'searchWarranties']);

        add_action('admin_enqueue_scripts', [$this->ajaxController, 'enqueueAdminAssets']);
    }
}