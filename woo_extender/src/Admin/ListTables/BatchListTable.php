<?php

declare(strict_types=1);

namespace WooExtender\Admin\ListTables;

use Override;
use WP_List_Table;
use WooExtender\Helpers\Sanitize;
use WooExtender\Services\BatchService;

defined('ABSPATH') || exit;

if (! class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class BatchListTable extends WP_List_Table
{

    public function __construct(private BatchService $batch)
    {
        parent::__construct([
            'singular' => 'batch',
            'plural'   => 'batches',
            'ajax'     => false
        ]);
    }

    #[Override]
    public function get_columns(): array
    {
        return [
            'cb'           => '<input type="checkbox" />',
            'product_id' => __('Product', 'woo-extender'),
            'variation_id' => __('Variation', 'woo-extender'),
            'supplier_id' => __('Supplier', 'woo-extender'),
            'sku' => __('Batch SKU', 'woo-extender'),
            'quantity_total' => __('Total', 'woo-extender'),
            'quantity_available' => __('Available', 'woo-extender'),
            'warranty_end_date' => __('Warranty\'s End', 'woo-extender'),
            'sell_price' => __('Sell Price', 'woo-extender'),
            'buy_price' => __('Buy Price', 'woo-extender'),
            'cost_total' => __('Total Cost', 'woo-extender'),
            'purchase_date' => __('Purchase Date', 'woo-extender'),
            'created_at' => __('Date Recorded', 'woo-extender'),
        ];
    }

    #[Override]
    protected function get_sortable_columns()
    {
        return [
            'warranty_end_date' => ['warranty_end_date', true],
            'purchase_date' => ['purchase_date', true],
        ];
    }

    public function column_product_id(object $item): string
    {
        $base_url   = admin_url('admin.php?page=woo-extender-batches');
        $edit_url   = add_query_arg(['action' => 'edit', 'id' => $item->id], $base_url);
        $delete_url = add_query_arg([
            'action'           => 'delete',
            'id'               => $item->id,
            '_wpnonce-batches' => wp_create_nonce('bulk-batches_' . $item->id)
        ], $base_url);

        $actions = [
            'edit'   => sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'woo-extender')),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'%s\')">%s</a>',
                esc_url($delete_url),
                __('Are you sure to delete this batch?', 'woo-extender'),
                __('Delete', 'woo-extender')
            ),
        ];

        return sprintf(
            '<strong><a class="row-title" href="%s">%s</a></strong> %s',
            esc_url($edit_url),
            esc_html(get_the_title($item->product_id)),
            $this->row_actions($actions)
        );
    }

    public function column_variation_id(object $item): array|string
    {
        if (empty($item->variation_id)) return '-';
        $variation = wc_get_product($item->variation_id);
        return ucfirst(implode(' - ', $variation->get_attributes()));
    }

    public function column_supplier_id(object $item): ?string
    {
        return woo_extender_get_supplier_name((int) $item->supplier_id);
    }

    public function column_cb($item): string
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', (int) $item->id);
    }

    #[Override]
    public function get_bulk_actions(): array
    {
        return [
            'delete' => __('Delete', 'woo-extender')
        ];
    }

    #[Override]
    public function no_items(): void
    {
        esc_html_e('No batches found.', 'woo-extender');
    }

    #[Override]
    public function column_default($item, $column_name): string
    {
        return ! empty($item->$column_name) ? esc_html($item->$column_name) : '-';
    }


    #[Override]
    public function prepare_items(): void
    {
        $per_page = 20;
        $current_page = $this->get_pagenum();

        $args = [
            'limit'   => $per_page,
            'paged'   => $current_page,
            'search'  => isset($_REQUEST['s']) ? Sanitize::string($_REQUEST['s']) : '',
            'orderby' => isset($_REQUEST['orderby']) ? Sanitize::string($_REQUEST['orderby']) : 'id',
            'order'   => isset($_REQUEST['order']) ? Sanitize::string($_REQUEST['order']) : 'DESC',
            'status'  => 1
        ];

        $this->items = $this->batch->getList($args);
        $items_count = $this->batch->getCount($args);

        $this->set_pagination_args([
            'total_items' => $items_count,
            'per_page' => $per_page,
            'total_pages' => ceil($items_count / $per_page)
        ]);
    }
}