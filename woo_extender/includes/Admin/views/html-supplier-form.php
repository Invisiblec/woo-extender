<?php

use WooExtender\Services\SupplierServices as Supplier;

defined('ABSPATH') || exit;

$title = (!empty($action) && $action === 'edit') ? __('Edit supplier', 'woo-extender') : __('Add new supplier', 'woo-extender');
$fields = (new Supplier)->get_form_fields();
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=woo-extender-suppliers')); ?>">
        <?php wp_nonce_field('save_supplier_action', 'supplier_nonce_field'); ?>

        <?php if (isset($supplier_id) && $supplier_id > 0): ?>
        <input type="hidden" name="id" value="<?php echo absint($supplier_id); ?>">
        <?php endif; ?>

        <?php if (isset($_GET['page'])): ?>
        <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
        <?php endif; ?>

        <table class="form-table" role="presentation">
            <tbody>
                <?php foreach ($fields as $field_key => $field_meta): ?>
                <?php
                    $field_value = $supplier ? $supplier->$field_key : '';
                    $is_required = $field_meta['required'] ? 'required' : '';
                    ?>
                <tr>
                    <th scope="row">
                        <label
                            for="field_<?php echo esc_attr($field_key); ?>"><?php echo esc_html($field_meta['label']); ?>
                            <?php if ($field_meta['required']) : ?>
                            <span class="description" style="color: red;">*</span>
                            <?php endif; ?>
                        </label>
                    </th>
                    <td>
                        <?php if ($field_meta['type'] === 'textarea'): ?>
                        <textarea name="<?php echo esc_attr($field_key); ?>"
                            id="field_<?php echo esc_attr($field_key); ?>" rows="4" class="large-text"
                            <?php echo $is_required; ?>><?php echo esc_textarea($field_value); ?></textarea>
                        <?php else : ?>
                        <input name="<?php echo esc_attr($field_key); ?>"
                            type="<?php echo esc_attr($field_meta['type']); ?>"
                            id="field_<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr($field_value); ?>"
                            class="regular-text" <?php echo $is_required; ?>>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit-suppliers" id="submit" class="button button-primary"
                value="<?php (! empty($action) && $action === 'edit') ? esc_attr_e('Update', 'woo-extender') : esc_attr_e('Save', 'woo-extender'); ?>">
            <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-suppliers')); ?>"
                class="button button-secondary"><?php esc_html_e('Cancel', 'woo-extender'); ?></a>
        </p>
    </form>
</div>