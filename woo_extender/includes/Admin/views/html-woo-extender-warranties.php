<?php

defined('ABSPATH') || exit;

use WooExtender\Helpers\Sanitize;
use WooExtender\Services\WarrantyService as Warranty;

$action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';

if (in_array($action, ['new', 'edit'], true)) {

    $warranty_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $warranty = null;

    if ($action === 'edit' && $warranty_id > 0) {
        $warranty = (new Warranty)->get_by_id($warranty_id);
    }

    require WOO_EXTNDR_PATH . 'includes/Admin/views/html-warranty-form.php';
} else {
    global $warranty_table;
?>

    <div class="wrap">
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Warranty Provider', 'woo-extender'); ?>
        </h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-warranties&action=new')); ?>"
            class="page-title-action"><?php esc_html_e('Add Warranty Provider', 'woo-extender') ?>
        </a>

        <hr class="wp-header-end">
        <form method="get">
            <input type="hidden" name="page" value="woo-extender-warranties" />
            <?php wp_nonce_field('bulk-warranties', '_wpnonce-warranties'); ?>
            <?php $warranty_table->search_box(__('Search warranties', 'woo-extender'), 'search-id'); ?>
            <?php $warranty_table->display(); ?>
        </form>
    </div>

<?php
}
