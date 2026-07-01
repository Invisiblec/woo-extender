<?php

defined('ABSPATH') || exit;

use WooExtender\Services\SupplierServices as Supplier;
use WooExtender\Helpers\Sanitize;

$action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';

if (in_array($action, ['new', 'edit'], true)) {

    $supplier_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $supplier = null;

    if ($action === 'edit' && $supplier_id > 0) {
        $supplier = (new Supplier)->get_by_id($supplier_id);
    }

    require WOO_EXTNDR_PATH . 'includes/Admin/views/html-supplier-form.php';
} else {
    global $supplier_table;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e('Suppliers', 'woo-extender') ?>
    </h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-suppliers&action=new')); ?>"
        class="page-title-action"><?php esc_html_e('Add Supplier', 'woo-extender') ?>
    </a>

    <hr class="wp-header-end">
    <form method="get">
        <input type="hidden" name="page" value="woo-extender-suppliers" />
        <?php wp_nonce_field('bulk-suppliers', '_wpnonce-suppliers'); ?>
        <?php $supplier_table->search_box(__('Search suppliers', 'woo-extender'), 'search-id'); ?>
        <?php $supplier_table->display(); ?>
    </form>
</div>

<?php
}