<?php

declare(strict_types=1);

namespace WooExtender\Admin;

use InvalidArgumentException;

defined('ABSPATH') || exit;

class FlashNotices
{
    private array $allowed_types = ['success', 'warning', 'info', 'error'];

    private function getNoticeKey(): string
    {
        return 'woo_extender_notice_' . get_current_user_id();
    }

    /**
     * Flash message: stored for the current user, shown once on the next page load (PRG pattern).
     *
     * @throws InvalidArgumentException When $type is not one of the allowed notice types.
     */
    public function add(string $type, string|array $messages): void
    {
        if (!in_array($type, $this->allowed_types, true)) {
            throw new InvalidArgumentException(sprintf("Invalid notice type \"%s\". Allowed: success, warning, info, error", $type));
        }

        $notices = get_transient($this->getNoticeKey()) ?: [];

        $notices[$type] = array_merge($notices[$type] ?? [], (array) $messages);

        set_transient($this->getNoticeKey(), $notices, MINUTE_IN_SECONDS);
    }

    public function display(): void
    {
        $notices = get_transient($this->getNoticeKey());

        if (empty($notices)) return;

        delete_transient($this->getNoticeKey());

        foreach ($notices as $type => $messageList) {
            printf('<div class="notice notice-%s is-dismissible">', esc_attr($type));
            foreach ($messageList as $message) {
                printf('<p>%s</p>', esc_html($message));
            }
            echo '</div>';
        }
    }
}