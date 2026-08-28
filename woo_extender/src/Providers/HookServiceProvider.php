<?php

declare(strict_types=1);

namespace WooExtender\Providers;

use WooExtender\Core\Container;
use WooExtender\Interfaces\HookSubscriberInterface;
use WooExtender\Interfaces\ServiceProviderInterface;
use WooExtender\Hooks\Admin\AjaxHookSubscriber;
use WooExtender\Hooks\Admin\MenuHookSubscriber;
use WooExtender\Hooks\Admin\BatchHookSubscriber;
use WooExtender\Hooks\Admin\InventoryHookSubscriber;
use WooExtender\Hooks\Admin\SupplierHookSubscriber;
use WooExtender\Hooks\Admin\WarrantyHookSubscriber;
use WooExtender\Hooks\Admin\ProductDataTabHookSuscriber;

class HookServiceProvider implements ServiceProviderInterface
{
    protected array $classes = [
        MenuHookSubscriber::class,
        AjaxHookSubscriber::class,
        BatchHookSubscriber::class,
        SupplierHookSubscriber::class,
        WarrantyHookSubscriber::class,
        ProductDataTabHookSuscriber::class,
        InventoryHookSubscriber::class,
    ];

    public function register(Container $container): void
    {
        foreach ($this->classes as $class) {
            $instance = $container->get($class);

            if ($instance instanceof HookSubscriberInterface) {
                $instance->subscribe();
            }
        }
    }
}