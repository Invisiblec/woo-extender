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

$title = (! empty($action) && $action === 'edit') ? __('Edit Batch', 'woo-extender') : __('Add New Batch', 'woo-extender');
?>

<div class="wrap">
    <h1><?php esc_html_e($title) ?></h1>
    <form action="<?php echo esc_url(admin_url('admin.php?page=woo-extender-batches')) ?>" method="post">
        <?php wp_nonce_field('save_batch_action', 'batch_nonce_field') ?>

        <?php if (isset($id) && $id > 0): ?>
        <input type="hidden" name="id" value="<?php echo absint($id) ?>">
        <?php endif; ?>

        <?php if (isset($_GET['page'])): ?>
        <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
        <?php endif; ?>

        <?php if (isset($_GET['action'])): ?>
        <input type="hidden" name="form_action" value="<?php echo $_GET['action'] ?>">
        <?php endif; ?>

        <table>
            <tbody>
                <?php foreach ($fields as $field_key => $field_meta):
                    $field_value = $item ? ($item->{$field_key} ?? '') : '';
                    $is_required = $field_meta['required'] ? 'required' : '';
                    $is_editable = '';
                    if ($action === 'edit'):
                        $is_editable = $field_meta['editable'] ? '' : 'disabled';
                    endif;
                    $field_type = $field_meta['ui_type'] ?? $field_meta['type'];
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
                        <?php if ($field_type === 'parent_product_search'): ?>
                        <select name="<?php echo esc_attr($field_key) ?>" id="field_<?php echo esc_attr($field_key) ?>"
                            class="woo-extender-parent-product-search" style="width: 100%;"
                            data-placeholder="<?php esc_attr_e('Search for a product', 'woo-extender'); ?>"
                            data-action="<?php echo esc_attr($field_meta['action']); ?>" <?php echo $is_required ?>
                            <?php echo $is_editable; ?>>
                            <?php if (! empty($field_value)):
                                        $product_obj = wc_get_product($field_value);
                                        if ($product_obj): ?>
                            <option value="<?php echo esc_attr($field_value); ?>" selected="selected">
                                <?php echo esc_html($product_obj->get_name()); ?>
                            </option>
                            <?php endif;
                                    endif; ?>
                        </select>
                        <?php elseif ($field_type === 'variation_product_search'):
                                $parent_id = $item ? $item->product_id : 0;
                                $is_disabled = empty($parent_id) ? 'disabled' : ''; ?>
                        <select class="woo-extender-variation-select" style="width: 100%;"
                            name="<?php echo esc_attr($field_key); ?>" id="field_<?php echo esc_attr($field_key); ?>"
                            data-action="<?php echo esc_attr($field_meta['action']); ?>"
                            data-selected="<?php echo esc_attr($field_value); ?>" <?php echo $is_disabled; ?>
                            <?php echo $is_editable; ?>>
                            <option value=""><?php esc_html_e('-- Select Variation (Optional) --', 'woo-extender'); ?>
                            </option>
                            <?php
                                    if (!empty($parent_id)) {
                                        $product = wc_get_product($parent_id);
                                        if ($product && $product->is_type('variable')) {
                                            foreach ($product->get_children() as $variation_id) {
                                                $variation = wc_get_product($variation_id);
                                                if (!$variation) {
                                                    continue;
                                                }

                                                $attributes = implode(', ', $variation->get_attributes());
                                                $selected = ($field_value == $variation->get_id()) ? 'selected="selected"' : '';
                                                echo '<option value="' . esc_attr($variation->get_id()) . '" ' . $selected . '>' . esc_html(ucfirst($attributes)) . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                        </select>
                        <?php elseif ($field_type === 'ajax_select'): ?>
                        <select class="woo-extender-<?php echo esc_attr(str_replace('_', '-', $field_key)) ?>-select"
                            name="<?php echo esc_attr($field_key); ?>" id="field_<?php echo esc_attr($field_key); ?>"
                            data-placeholder="<?php esc_attr_e("Search for a {$field_meta['label']}") ?>"
                            data-action="<?php echo esc_attr($field_meta['action']); ?>" <?php echo $is_required ?>
                            <?php echo $is_editable; ?> style="width: 100%">
                            <?php if (! empty($field_value)): ?>
                            <option value="<?php echo esc_attr($field_value); ?>" selected="selected">
                                <?php if ($field_key === 'supplier_id'): ?>
                                <?php echo esc_html(woo_extender_get_supplier_name($field_value)); ?>
                                <?php elseif ($field_key === 'warranty_provider_id'): ?>
                                <?php echo esc_html($warranty_provider_name); ?>
                                <?php endif; ?>
                                <?php endif; ?>
                        </select>
                        <?php elseif ($field_type === 'textarea'): ?>
                        <textarea name="<?php echo esc_attr($field_key); ?>"
                            id="field_<?php echo esc_attr($field_key); ?>" rows="4" class="large-text"
                            <?php echo $is_required; ?>
                            <?php echo $is_editable; ?>> <?php echo esc_textarea($field_value); ?></textarea>
                        <?php else : ?>
                        <input name="<?php echo esc_attr($field_key); ?>" type="<?php echo esc_attr($field_type); ?>"
                            id="field_<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr($field_value); ?>"
                            class="regular-text" <?php echo $is_required; ?> <?php echo $is_editable; ?>>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit-batches" id="submiter" class="button button-primary"
                value="<?php (!empty($action) && $action === 'edit') ? esc_attr_e('Update', 'woo-extender') : esc_attr_e('Save', 'woo-extender'); ?>">
            <a href="<?php echo esc_url(admin_url('admin.php?page=woo-extender-batches')); ?>"
                class="button button-secondary"><?php esc_html_e('Cancel', 'woo-extender'); ?></a>
        </p>
    </form>
</div>