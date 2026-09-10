<?php

declare(strict_types=1);

namespace WooExtender\DTO;

defined('ABSPATH') || exit;

class Changeset
{
    public function __construct(
        public readonly int $id,
        public readonly array $changes
    ) {}
}