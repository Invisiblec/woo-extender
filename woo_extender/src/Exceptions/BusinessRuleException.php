<?php

declare(strict_types=1);

namespace WooExtender\Exceptions;

use Exception;

defined('ABSPATH') || exit;

/**
 * An expected refusal by a business rule (e.g. deleting a batch that has sales).
 * The message is shown to the user, so it must be translated and safe to display.
 */
class BusinessRuleException extends Exception {}
