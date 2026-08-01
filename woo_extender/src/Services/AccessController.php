<?php

declare(strict_types=1);

namespace WooExtender\Services;

defined('ABSPATH') || exit;

use WooExtender\Enums\Pages;

class AccessController
{
    public static function canCurrentUserAccess(Pages $page): bool
    {
        $capabilities = $page->getCapabilities();

        if (is_array($capabilities)) {
            foreach ($capabilities as $cap) {
                if (current_user_can($cap)) {
                    return true;
                }
            }
            return false;
        }

        return current_user_can($capabilities);
    }

    public static function validateNonce(string $action, string $field): void
    {
        if (! isset($_REQUEST[$field]) || ! wp_verify_nonce($_REQUEST[$field], $action)) {
            wp_die(__('Unauthorized access or security token has expired.', 'woo-extender'), __('Access Denied', 'woo-extender'), ['response' => 403]);
        }
    }
}