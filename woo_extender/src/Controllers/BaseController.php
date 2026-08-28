<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use WooExtender\Admin\Pages\PageRenderer;
use WooExtender\Core\WooExtender;
use WooExtender\Enums\Pages;
use WooExtender\Helpers\Sanitize;
use WooExtender\Services\AccessController;

defined('ABSPATH') || exit;

/**
 * @template TService
 */
abstract class BaseController
{
    abstract protected function getPage(): Pages;
    abstract protected function getNonceAction(): string;
    abstract protected function getNonceField(): string;
    abstract protected function getTableNonceAction(): string;
    abstract protected function getTableNonceField(): string;
    abstract protected function getFactoryClass(): string;

    abstract protected function getClassPrefix(): string;
    abstract protected static function getGlobalVar(): string;

    protected function getRenderData(object $item): array
    {
        return [];
    }

    /**
     * @param TService $service
     */
    public function __construct(protected object $service) {}

    public function registerTableLoader(): void
    {
        $slug_prefix = 'woo-extender';
        $page_slug   = $this->getPage()->value;
        $page_hook   = "{$slug_prefix}_page_{$slug_prefix}-{$page_slug}";

        add_action("load-{$page_hook}", [$this, 'loadPageTable']);
    }

    public function loadPageTable(): void
    {
        $class_prefix = $this->getClassPrefix();
        $global_var   = static::getGlobalVar();

        $action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';

        if (! in_array($action, ['new', 'edit'], true)) {
            $class_name = "WooExtender\\Admin\\ListTables\\{$class_prefix}ListTable";

            if (class_exists($class_name)) {
                $GLOBALS[$global_var] = WooExtender::make($class_name);
                $GLOBALS[$global_var]->prepare_items();
            }
        }
    }

    public function dispatch(): void
    {

        $page = $this->getPage();
        $page_slug = 'woo-extender-' . $page->value;

        if (! isset($_REQUEST['page']) || $_REQUEST['page'] !== $page_slug) return;

        if (! AccessController::canCurrentUserAccess($page)) {
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

    public function render(Pages $page): void
    {

        $action = isset($_GET['action']) ? Sanitize::string($_GET['action']) : 'list';
        $id     = isset($_GET['id']) ? Sanitize::int($_GET['id']) : 0;

        $view_data = [
            'action' => $action,
            'id'     => $id,
        ];

        $childData = null;

        if (in_array($action, ['new', 'edit'], true)) {
            $view_data['item'] = ($action === 'edit' && $id > 0) ? $this->service->getById($id) : null;
            if (! empty($view_data['item'])) {
                $childData = $this->getRenderData($view_data['item']);
            }
            $view_data['fields'] = $this->service?->getFormFields();
        } else {
            $global_var = static::getGlobalVar();
            $view_data['list_table'] = $GLOBALS[$global_var] ?? null;
        }

        if (! empty($childData)) {
            $view_data = array_merge($view_data, $childData);
        }

        PageRenderer::render($page, $view_data);
    }

    protected function submission(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error');
        }

        $nonce_action = $this->getNonceAction();
        $nonce_field = $this->getNonceField();

        AccessController::validateNonce($nonce_action, $nonce_field);

        $factory_class = $this->getFactoryClass();

        $fields  = $this->service->getFormFields();

        try {
            $dto = $factory_class::createDTO($_POST, $fields);

            if (! $this->service->save($dto)) {
                throw new \Exception(__('Failed to save the data. Please try again.', 'woo-extender'));
            }

            $this->redirect('success');
        } catch (\WooExtender\Exceptions\ValidationException $e) {
            $this->redirect('error');
        } catch (\Exception $e) {
            wp_die($e->getMessage(), __('Save Error', 'woo-extender'), ['response' => 500]);
        }
    }

    protected function destroy(): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->redirect('error');
        }

        $id = isset($_GET['id']) ? Sanitize::int($_GET['id']) : null;
        $nonce_action = $id ? $this->getTableNonceAction() . "_{$id}" : $this->getTableNonceAction();
        $nonce_field  = $this->getTableNonceField();

        AccessController::validateNonce($nonce_action, $nonce_field);

        $selected_ids = [];

        if (isset($_GET['bulk-delete']) && is_array($_GET['bulk-delete'])) {
            $selected_ids = array_map('intval', $_GET['bulk-delete']);
        } elseif ($id) {
            $selected_ids = [$id];
        }

        foreach ($selected_ids as $id) {
            $supplier = $this->service->getById($id);

            if ($supplier) $this->service->delete($supplier);
        }

        $this->redirect('deleted');
    }

    protected function redirect(string $message): void
    {
        $page_slug = 'woo-extender-' . $this->getPage()->value;
        wp_safe_redirect(admin_url("admin.php?page={$page_slug}&message={$message}"));
        exit;
    }
}