<?php

defined('ABSPATH') || exit;

use WooExtender\Services\BatchServices as Batch;
use WooExtender\Helpers\Sanitize;

$action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';

if (in_array($action, ['new', 'edit'], true)) {

    $batch_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $batch = null;

    if ($action === 'edit' && $batch_id > 0) {
        $batch = (new Batch)->get_by_id($batch_id);
    }

    require WOO_EXTNDR_PATH . 'includes/Admin/views/html-batch-form.php';
} else {
    global $batch_table;
?>

    <div class="wrap">
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Batches', 'woo-extender') ?>
        </h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-batches&action=new')); ?>"
            class="page-title-action"><?php esc_html_e('Add Batch', 'woo-extender') ?>
        </a>

        <hr class="wp-header-end">
        <form method="get">
            <input type="hidden" name="page" value="woo-extender-batches" />
            <?php wp_nonce_field('bulk-batches', '_wpnonce-batches'); ?>
            <?php $batch_table->search_box(__('Search batches', 'woo-extender'), 'search-id'); ?>
            <?php $batch_table->display(); ?>
        </form>
    </div>

<?php
}
