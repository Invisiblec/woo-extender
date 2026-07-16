<?php

namespace WooExtender\DTO;

defined('ABSPATH') || exit;

class BatchData
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $supplier_id,
        public readonly int $quantity_total = 0,
        public readonly int $quantity_reserved = 0,
        public readonly int $quantity_sold = 0,
        public readonly float $buy_price = 0,
        public readonly ?int $id = null,
        public readonly ?int $warranty_id = null,
        public readonly ?float $sell_price = null,
        public readonly ?\DateTime $warranty_start_date = null,
        public readonly ?\DateTime $warranty_end_date = null,
        public readonly ?\DateTime $purchase_date = null,
        public readonly ?int $variation_id = null,
        public readonly ?string $sku = null,
    ) {}
}