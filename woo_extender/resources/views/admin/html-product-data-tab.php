<?php

defined('ABSPATH') || exit;

global $product_object;
$features = $product_object?->get_meta('_woo_extndr_product_main_features');
?>

<div id="woo_extndr_product_data" class="panel woocommerce_options_panel hidden">
    <div class="options_group">
        <?php woocommerce_wp_text_input([
            'id' => 'woo_extndr_eng_title',
            'label' => __('English Title', 'woo-extender'),
            'palceholder' => __('Enter your product english title here', 'woo-extender'),
            'value' => $product_object?->get_meta('_woo_extndr_english_title') ?? '',
        ]) ?>
    </div>

    <div class="options_group">
        <div class="woo_extndr_product_main_features_container d-flex flex-column gap-1">
            <div class="woo_extndr_product_main_features_heading d-flex">
                <strong class="flex-fill"><?php _e('Title', 'woo-extender') ?></strong>
                <strong class="flex-fill"><?php _e('Value', 'woo-extender') ?></strong>
            </div>
            <?php if (!empty($features) && is_array($features)): ?>
            <?php foreach ($features as $index => $feature): ?>
            <div class="woo_extndr_product_main_features_row d-flex gap-1">
                <input type="text" name="woo_extndr_product_main_feature[<?php echo esc_attr($index) ?>][title]"
                    value="<?php esc_attr_e($feature['title'] ?? '') ?>" class="short">
                <input type="text" name="woo_extndr_product_main_feature[<?php echo esc_attr($index) ?>][value]"
                    value="<?php esc_attr_e($feature['value'] ?? '') ?>" class="short">
                <button type="button" class="button woo_extndr_feature_delete_btn">
                    <?php _e('Delete', 'woo-extender') ?>
                </button>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="woo_extndr_product_main_features_row d-flex gap-1">
                <input type="text" name="woo_extndr_product_main_feature[0][title]" class="short">
                <input type="text" name="woo_extndr_product_main_feature[0][value]" class="short">
                <button type="button" class="button woo_extndr_feature_delete_btn">
                    <?php _e('Delete', 'woo-extender') ?>
                </button>
            </div>
            <?php endif; ?>
            <button type="button" id="woo_extndr_feature_add_btn"
                class="button-primary"><?php _e('Add feature', 'woo-extender') ?></button>
        </div>
    </div>
</div>