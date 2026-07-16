<?php

namespace WooExtender\Controllers;

use stdClass;
use WooExtender\Enums\Pages;
use WooExtender\Helpers\Sanitize;
use WooExtender\Services\AccessController;

defined('ABSPATH') || exit;

abstract class BaseController
{

    abstract protected function get_page(): Pages;
    abstract protected function get_nonce_action(): string;
    abstract protected function get_nonce_field(): string;
    abstract protected function get_table_nonce_action(): string;
    abstract protected function get_table_nonce_field(): string;
    abstract protected function get_service_class(): string;

    abstract protected function get_class_prefix(): string;
    abstract protected static function get_global_var(): string;

    public function __construct()
    {
        add_action('admin_init', [$this, 'dispatch']);
        add_action('admin_menu', [$this, 'register_table_loader']);
    }

    public function register_table_loader(): void
    {
        $slug_prefix = 'woo-extender';
        $page_slug   = $this->get_page()->value;
        $page_hook   = "{$slug_prefix}_page_{$slug_prefix}-{$page_slug}";

        add_action("load-{$page_hook}", [$this, 'load_page_table']);
    }

    public function load_page_table(): void
    {
        $class_prefix = $this->get_class_prefix();
        $global_var   = static::get_global_var();

        $action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';

        if (! in_array($action, ['new', 'edit'], true)) {
            $class_name = "WooExtender\\Admin\\ListTables\\{$class_prefix}ListTable";

            if (class_exists($class_name)) {
                $GLOBALS[$global_var] = new $class_name();
                $GLOBALS[$global_var]->prepare_items();
            }
        }
    }

    public function dispatch(): void
    {

        $page = $this->get_page();
        $page_slug = 'woo-extender-' . $page->value;

        if (! isset($_REQUEST['page']) || $_REQUEST['page'] !== $page_slug) return;

        if (! AccessController::can_current_user_access($page)) {
            wp_die(__('You have not access to this page.', 'woo-extender'), __('Access Error', 'woo-extender'), ['response' => 403]);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['submit-' . $page->value])) {
                $this->submission();
                return;
            }

            $bulk_action = $_POST['action'] ?? $_POST['action2'] ?? '';
            if ($bulk_action === 'delete') {
                $this->destroy();
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $action = $_GET['action'] ?? '';
            if ($action === 'delete') {
                $this->destroy();
                return;
            }
        }
    }

    protected function submission(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error');
        }

        $nonce_action = $this->get_nonce_action();
        $nonce_field = $this->get_nonce_field();

        AccessController::validate_nonce($nonce_action, $nonce_field);

        $service_class = $this->get_service_class();
        $service = new $service_class();

        $fields  = $service->get_form_fields();

        $dto = $this->build_dto($fields);

        if (isset($_POST['id']) && Sanitize::int($_POST['id']) > 0) {
            $dto->id = Sanitize::int($_POST['id']);
        }

        if (! $service->save($dto)) {
            wp_die(__('Failed to save the data. Please try again.', 'woo-extender'), __('Save Error', 'woo-extender'), ['response' => 500]);
        }

        $this->redirect('success');
    }

    protected function destroy(): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->redirect('error');
        }

        $id = isset($_GET['id']) ? Sanitize::int($_GET['id']) : null;
        $nonce_action = $id ? $this->get_table_nonce_action() . "_{$id}" : $this->get_table_nonce_action();
        $nonce_field  = $this->get_table_nonce_field();

        AccessController::validate_nonce($nonce_action, $nonce_field);

        $service_class = $this->get_service_class();
        $service = new $service_class();

        $selected_ids = [];

        if (isset($_GET['bulk-delete']) && is_array($_GET['bulk-delete'])) {
            $selected_ids = array_map('intval', $_GET['bulk-delete']);
        } elseif ($id) {
            $selected_ids = [$id];
        }

        foreach ($selected_ids as $id) {
            $supplier = $service->get_by_id($id);

            if ($supplier) $service->delete($supplier);
        }

        $this->redirect('deleted');
    }

    protected function redirect(string $message): void
    {
        $page_slug = 'woo-extender-' . $this->get_page()->value;
        wp_safe_redirect(admin_url("admin.php?page={$page_slug}&message={$message}"));
        exit;
    }

    private function build_dto(array $fields): ?stdClass
    {
        $dto = new \stdClass;

        foreach ($fields as $field_key => $field_meta) {

            $key = $_POST[$field_key] ?? '';
            $value = Sanitize::field($key, $field_meta['type']);

            if ($field_meta['required'] && empty($value)) {
                wp_die(
                    sprintf(
                        __('The "%s" field is required and cannot be empty.', 'woo-extender'),
                        esc_html($field_meta['label'])
                    ),
                    __('Validation Error', 'woo-extender'),
                    ['response' => 400]
                );
            }

            $dto->{$field_key} = $value;
        }

        return $dto;
    }
}
