<?php

declare(strict_types=1);

namespace WooExtender\Interfaces;

defined('ABSPATH') || exit;

interface HookSubscriberInterface
{
    public function subscribe(): void;
}