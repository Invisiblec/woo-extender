<?php

declare(strict_types=1);

namespace WooExtender\Helpers;

use DateTimeImmutable;

defined('ABSPATH') || exit;

class Sanitize
{
    public static function field(mixed $value, string $type = 'text'): mixed
    {
        if (is_array($value)) {
            return array_map(fn($item) => self::field($item, $type), $value);
        }

        return match ($type) {
            'text', 'string'            => self::string($value),
            'int', 'integer', 'number'  => is_numeric($value) ? self::int($value) : 0,
            'float', 'double'           => is_numeric($value) ? self::float($value) : 0.0,
            'email'                     => self::email($value),
            'url'                       => self::url($value),
            'textarea'                  => self::textarea($value),
            'date', 'month'             => self::date($value),
            default                     => self::string($value),
        };
    }

    public static function string(string $value): string
    {
        return sanitize_text_field($value);
    }

    public static function int(mixed $value): int
    {
        return absint($value);
    }

    public static function float(float $value): float
    {
        return floatval($value);
    }

    public static function email(string $value): string
    {
        return sanitize_email($value);
    }

    public static function url(string $value): string
    {
        return esc_url_raw($value);
    }

    public static function textarea(string $value): string
    {
        return sanitize_textarea_field($value);
    }

    public static function date(string $value, string $format = 'Y-m-d'): ?string
    {
        $value = sanitize_text_field($value);

        $dateObject = DateTimeImmutable::createFromFormat('!' . $format, $value);

        if ($dateObject && $dateObject->format($format) === $value) {
            return $dateObject->format($format);
        }

        return null;
    }
}