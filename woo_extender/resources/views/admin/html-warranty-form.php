<?php

defined('ABSPATH') || exit;

/**
 * @var string      $action
 * @var int         $id
 * @var mixed|null  $item
 * @var array|null  $fields
 * @var object|null $list_table
 */

$title = (! empty($action) && $action === 'edit') ? __('Edit Warranty Provider', 'woo-extender') : __('Add new Warranty provider', 'woo-extender');
?>

<div class="wrap">
    <h1><?php echo esc_html($title) ?></h1>
    <form action="<?php echo esc_url(admin_url('admin.php?page=woo-extender-warranties')); ?>" method="post">
        <?php wp_nonce_field('save_warranty_action', 'warranty_nonce_field'); ?>

        <?php if (isset($id) && $id > 0): ?>
        <input type="hidden" name="id" value="<?php echo absint($id); ?>">
        <?php endif; ?>

        <?php if (isset($_GET['page'])): ?>
        <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
        <?php endif; ?>

        <table class="form-table" role="presentation">
            <tbody>
                <?php foreach ($fields as $field_key => $field_meta): ?>
                <?php
                    $field_value = $item ? $item->$field_key : '';
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
            <input type="submit" name="submit-warranties" id="submiter" class="button button-primary"
                value="<?php (!empty($action) && $action === 'edit') ? esc_attr_e('Update', 'woo-extender') : esc_attr_e('Save', 'woo-extender'); ?>">
            <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-warranties')); ?>"
                class="button button-secondary"><?php esc_html_e('Cancel', 'woo-extender'); ?></a>
        </p>
    </form>
</div>