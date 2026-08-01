<?php

declare(strict_types=1);

namespace WooExtender\DTO;

defined('ABSPATH') || exit;

class SupplierData
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?int $id = null,
        public readonly ?string $phone = null,
        public readonly ?string $sales_person = null,
        public readonly ?string $sales_phone = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
        public readonly ?string $address = null,
        public readonly ?string $notes = null,
    ) {}
}
