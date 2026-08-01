<?php

declare(strict_types=1);

namespace WooExtender\Providers;

use WooExtender\Core\Container;
use WooExtender\Repository\SupplierRepository;
use WooExtender\Repository\WarrantyRepository;
use WooExtender\Repository\BatchRepository;

class RepositoryServiceProvider
{
    public function register(Container $container): void
    {
        $container->set('SupplierRepository', SupplierRepository::class);
        $container->set('WarrantyRepository', WarrantyRepository::class);
        $container->set('BatchRepository', BatchRepository::class);
    }
}