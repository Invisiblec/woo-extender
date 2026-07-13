<?php

namespace WooExtender\Admin\Controller;

use Override;
use WooExtender\Enums\Pages;
use WooExtender\Services\BatchService as Batch;

defined('ABSPATH') || exit;

class BatchController extends BaseController
{
    #[Override]
    protected function get_page(): Pages
    {
        return Pages::Batches;
    }

    #[Override]
    protected function get_nonce_action(): string
    {
        return 'save_batch_action';
    }

    #[Override]
    protected function get_nonce_field(): string
    {
        return 'batch_nonce_field';
    }

    #[Override]
    protected function get_table_nonce_action(): string
    {
        return 'bulk-batches';
    }

    #[Override]
    protected function get_table_nonce_field(): string
    {
        return '_wpnonce-batches';
    }

    #[Override]
    protected function get_service_class(): string
    {
        return Batch::class;
    }

    #[Override]
    protected function get_class_prefix(): string
    {
        return 'Batch';
    }

    #[Override]
    protected static function get_global_var(): string
    {
        return 'batch_table';
    }
}
