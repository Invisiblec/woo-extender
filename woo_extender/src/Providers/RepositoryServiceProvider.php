<?php

declare(strict_types=1);

namespace WooExtender\Providers;

use WooExtender\Core\Container;
use WooExtender\Interfaces\ServiceProviderInterface;
use WooExtender\Repositories\SupplierRepository;
use WooExtender\Repositories\WarrantyRepository;
use WooExtender\Repositories\BatchRepository;

class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(SupplierRepository::class);
        $container->set(WarrantyRepository::class);
        $container->set(BatchRepository::class);
    }
}