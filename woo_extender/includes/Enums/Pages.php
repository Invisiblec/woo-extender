<?php

namespace WooExtender\Enums;

defined('ABSPATH') || exit;

enum Pages: string
{
    case Home       = 'admin';
    case Suppliers  = 'suppliers';
    case Warranties = 'warranties';
    case Batches    = 'batches';
    case Settings   = 'settings';

    public function getCapabilities(): string|array
    {
        return match ($this) {
            self::Home, self::Batches => ['manage_options', 'manage_woocommerce'],
            self::Suppliers, self::Warranties => 'edit_others_posts',
            self::Settings => 'manage_options'
        };
    }

    public function getCurrentUserCapability(): string
    {
        $capabilities = $this->getCapabilities();

        if (is_array($capabilities)) {
            foreach ($capabilities as $cap) {
                if (current_user_can($cap)) {
                    return $cap;
                }
            }
            return self::Settings->getCapabilities();
        };

        return $capabilities;
    }
}
