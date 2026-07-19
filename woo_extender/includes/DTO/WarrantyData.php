<?php

namespace WooExtender\DTO;

defined('ABSPATH') || exit;

class WarrantyData
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?int $id = null,
        public readonly ?string $phone = null,
        public readonly ?string $support_person = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
        public readonly ?int $warranty_default_months = null,
        public readonly ?string $notes = null,
    ) {}
}