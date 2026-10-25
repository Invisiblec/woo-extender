<?php

declare(strict_types=1);

namespace WooExtender\Hooks\Admin;

use Override;
use WooExtender\Admin\FlashNotices;
use WooExtender\Interfaces\HookSubscriberInterface;

defined('ABSPATH') || exit;

class NoticeHookSubscriber implements HookSubscriberInterface
{
    public function __construct(private FlashNotices $flashNotices) {}

    #[Override]
    public function subscribe(): void
    {
        add_action('admin_notices', [$this->flashNotices, 'display']);
    }
}
