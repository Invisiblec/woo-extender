<?php

declare(strict_types=1);

namespace WooExtender\Models;

defined('ABSPATH') || exit;

abstract class BaseModel
{
    public function __construct(protected array $attr = []) {}

    public function __get(string $key)
    {
        return $this->attr[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attr[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->attr[$key]);
    }

    public function toArray(): array
    {
        return $this->attr;
    }
}
