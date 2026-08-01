<?php

declare(strict_types=1);

namespace WooExtender\Admin\Pages;

use WooExtender\Enums\Pages;
use WooExtender\Services\AccessController;

defined('ABSPATH') || exit;

class PageRenderer
{

    public static function render(Pages $page, array $data = []): void
    {
        if (AccessController::canCurrentUserAccess($page)) {
            extract($data);
            require_once WOO_EXTNDR_PATH . 'resources/views/admin/html-woo-extender-' . $page->value . '.php';
            return;
        }

        wp_die(__('You have not access to this page.', 'woo-extender'), __('Access Error', 'woo-extender'), ['response' => 403]);
    }
}
