<?php

declare(strict_types=1);

namespace WooExtender\Admin\MetaBoxes;

use WooExtender\Helpers\Sanitize;

class ProductMetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register']);
    }

    public function register(): void
    {
        add_meta_box(
            'woo_extndr_box',
            __('Woo Extender', 'woo-extender'),
            [$this, 'render'],
            'product',
            'normal',
            'default'
        );
    }

    public function render($post): void
    {
        wp_nonce_field(
            'woo_extndr_nonce',
            'woo_extndr_nonce'
        );

        $value = get_post_meta(
            $post->ID,
            '_woo_extndr_field',
            true
        );

?>
        <input type="text" name="woo_extndr_field" value="<?php echo esc_attr($value); ?>" style="width:100%;">
<?php

        declare(strict_types=1);
    }

    public function save(int $post_id): void
    {
        if (
            ! isset($_POST['woo_extndr_nonce']) ||
            ! wp_verify_nonce(
                $_POST['woo_extndr_nonce'],
                'woo_extndr_nonce'
            )
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['woo_extndr_field'])) {
            update_post_meta(
                $post_id,
                '_woo_extndr_field',
                Sanitize::string($_POST['woo_extndr_field'])
            );
        }
    }
}
