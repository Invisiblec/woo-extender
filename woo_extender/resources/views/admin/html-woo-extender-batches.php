<?php

defined('ABSPATH') || exit;

/**
 * @var string      $action
 * @var int         $id
 * @var mixed|null  $item
 * @var array|null  $fields
 * @var object|null $list_table
 * @var string|null $warranty_provider_name 
 */

if (in_array($action, ['new', 'edit'], true)) {
    require WOO_EXTNDR_PATH . 'resources/views/admin/html-batch-form.php';
}
?>

<?php if (isset($list_table) && $list_table !== null) : ?>
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
        <?php $list_table->search_box(__('Search batches', 'woo-extender'), 'search-id'); ?>
        <?php $list_table->display(); ?>
    </form>
</div>
<?php endif; ?>