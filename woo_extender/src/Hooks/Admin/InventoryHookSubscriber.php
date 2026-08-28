<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Interfaces\HookSubscriberInterface;
use WooExtender\Services\InventoryService;

defined('ABSPATH') || exit;

class InventoryHookSubscriber implements HookSubscriberInterface
{
    public function __construct(public InventoryService $inventoryService) {}

    #[Override]
    public function subscribe(): void
    {
        add_action('woo_extender_after_batch_save', [$this->inventoryService, 'syncProductStock'], 10, 2);
    }
}