<?php

declare(strict_types=1);

namespace WooExtender\Exceptions;

use Exception;

defined('ABSPATH') || exit;

class ValidationException extends Exception
{
    public function __construct(private array $errors)
    {
        parent::__construct(__('Validation failed.', 'woo-extender'));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
