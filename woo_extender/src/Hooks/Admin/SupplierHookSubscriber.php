<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Controllers\SupplierController;
use WooExtender\Interfaces\HookSubscriberInterface;

defined('ABSPATH') || exit;

class SupplierHookSubscriber implements HookSubscriberInterface
{
    public function __construct(public SupplierController $controller) {}

    #[Override]
    public function subscribe(): void
    {
        add_action('admin_init', [$this->controller, 'dispatch']);
        add_action('admin_menu', [$this->controller, 'registerTableLoader']);
    }
}