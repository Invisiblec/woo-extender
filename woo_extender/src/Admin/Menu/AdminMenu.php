<?php

declare(strict_types=1);

namespace WooExtender\Admin\Menu;

use WooExtender\Admin\Pages\PageRenderer;
use WooExtender\Enums\Pages;

defined('ABSPATH') || exit;

class AdminMenu
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register']);
    }

    public function register(): void
    {
        $parent_slug = 'woo-extender-admin';

        add_menu_page(
            __('Woo Extender', 'woo-extender'),
            __('Woo Extender', 'woo-extender'),
            Pages::Home->getCurrentUserCapability(),
            $parent_slug,
            function () {
                PageRenderer::render(Pages::Home);
            },
            'dashicons-database',
            56
        );

        add_submenu_page(
            $parent_slug,
            __('Home', 'woo-extender'),
            __('Home', 'woo-extender'),
            Pages::Home->getCurrentUserCapability(),
            $parent_slug,
            ''
        );

        add_submenu_page(
            $parent_slug,
            __('Suppliers', 'woo-extender'),
            __('Suppliers', 'woo-extender'),
            Pages::Suppliers->getCurrentUserCapability(),
            'woo-extender-suppliers',
            function () {
                PageRenderer::render(Pages::Suppliers);
            }
        );

        add_submenu_page(
            $parent_slug,
            __('Warranty Providers', 'woo-extender'),
            __('Warranty Providers', 'woo-extender'),
            Pages::Warranties->getCurrentUserCapability(),
            'woo-extender-warranties',
            function () {
                PageRenderer::render(Pages::Warranties);
            }
        );

        add_submenu_page(
            $parent_slug,
            __('Batches', 'woo-extender'),
            __('Batches', 'woo-extender'),
            Pages::Batches->getCurrentUserCapability(),
            'woo-extender-batches',
            function () {
                PageRenderer::render(Pages::Batches);
            }
        );

        add_submenu_page(
            $parent_slug,
            __('Settings', 'woo-extender'),
            __('Settings', 'woo-extender'),
            Pages::Settings->getCurrentUserCapability(),
            'woo-extender-settings',
            function () {
                PageRenderer::render(Pages::Settings);
            }
        );
    }
}
