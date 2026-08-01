<?php

defined('ABSPATH') || exit;

/**
 * @var string      $action
 * @var int         $id
 * @var mixed|null  $item
 * @var array|null  $fields
 * @var object|null $list_table
 */

if (in_array($action, ['new', 'edit'], true)) {

    $supplier = null;
    require WOO_EXTNDR_PATH . 'resources/views/admin/html-supplier-form.php';
}
?>

<?php if (!empty($list_table) && $action === 'list'): ?>
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
        <?php $list_table->search_box(__('Search suppliers', 'woo-extender'), 'search-id'); ?>
        <?php $list_table->display(); ?>
    </form>
</div>
<?php endif; ?>