<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Admin\Menu\AdminMenu;
use WooExtender\Interfaces\HookSubscriberInterface;

defined('ABSPATH') || exit;

class MenuHookSubscriber implements HookSubscriberInterface
{
    public function __construct(public AdminMenu $adminMenu) {}

    #[Override]
    public function subscribe(): void
    {
        add_action('admin_menu', [$this->adminMenu, 'register']);
    }
}