<?php

namespace WooExtender\Admin\ListTables;

use Override;
use WP_List_Table;
use WooExtender\Services\WarrantyServices as Warranty;
use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

if (! class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WarrantyListTable extends WP_List_Table
{

    public function __construct()
    {
        parent::__construct([
            'singular' => 'warranty',
            'plural'   => 'warranties',
            'ajax'     => false
        ]);
    }

    #[Override]
    public function get_columns(): array
    {
        return [
            'cb'                      => '<input type="checkbox" />',
            'name'                    => __('Warranty Provider', 'woo-extender'),
            'slug'                    => __('Slug', 'woo-extender'),
            'support_phone'           => __('Support Phone', 'woo-extender'),
            'warranty_default_months' => __('Default Warranty Period (Months)', 'woo-extender'),
            'created_at'              => __('Date Recorded', 'woo-extedner')
        ];
    }

    #[Override]
    public function get_sortable_columns(): array
    {
        return [
            'name' => ['name', true],
            'created_at' => ['created_at', false]
        ];
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
        esc_html_e('No warranty provider found.', 'woo-extender');
    }

    #[Override]
    public function column_default($item, $column_name): string
    {
        return isset($item->$column_name) ? esc_html($item->$column_name) : '';
    }

    public function column_cb($item): string
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', (int) $item->id);
    }

    public function column_name(object $item): string
    {
        $base_url = admin_url('admin.php?page=woo-extender-warranties');
        $edit_url = add_query_arg(['action' => 'edit', 'id' => $item->id], $base_url);
        $delete_url = add_query_arg([
            'action' => 'delete',
            'id' => $item->id,
            '_wpnonce-warranties' => wp_create_nonce('bulk-warranties_' . $item->id),
        ], $base_url);

        $actions = [
            'edit'   => sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'woo-extender')),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'%s\')">%s<a/>',
                esc_url($delete_url),
                __('Are you sure to delete this warranty?', 'woo-extender'),
                __('Delete', 'woo-extender')
            )
        ];

        return sprintf(
            '<strong><a href="%s" class=""row-title>%s</a></strong> %s',
            esc_url($edit_url),
            esc_html($item->name),
            $this->row_actions($actions)
        );
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

        $this->items = (new Warranty)->get_list($args);
        $items_count = (new Warranty)->get_count($args);

        $this->set_pagination_args([
            'total_items' => $items_count,
            'per_page'    => $per_page,
            'total_pages' => ceil($items_count / $per_page)
        ]);
    }
}
