<?php

declare(strict_types=1);

namespace WooExtender\Controllers;

use WooExtender\Admin\FlashNotices;
use WooExtender\Admin\Pages\PageRenderer;
use WooExtender\Core\WooExtender;
use WooExtender\Enums\Pages;
use WooExtender\Exceptions\BusinessRuleException;
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
    public function __construct(protected object $service, protected FlashNotices $flashNotices) {}

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

        $is_edit = ($_POST['form_action'] ?? '') === 'edit';

        try {
            if ($is_edit) {
                $change_set = $factory_class::createChangeset($_POST, $fields);
                $result = $this->service->update($change_set);
            } else {
                $dto = $factory_class::createDTO($_POST, $fields);
                $result = $this->service->save($dto);
            }

            if (! $result) {
                throw new \RuntimeException('save()/update() returned false');
            }

            $this->flashNotices->add('success', $is_edit
                ? __('Item updated.', 'woo-extender')
                : __('Item created.', 'woo-extender'));
            $this->redirect('success');
        } catch (\WooExtender\Exceptions\ValidationException $e) {
            // Expected: the user filled a field wrong; show each field's message on the form.
            $this->flashNotices->add('error', array_values($e->getErrors()));
            $this->redirectBack();
        } catch (\Throwable $e) {
            // Unexpected: details go to the log, the user gets a generic message.
            error_log('[woo-extender] ' . $e->getMessage());
            $this->flashNotices->add('error', __('Failed to save the data. Please try again.', 'woo-extender'));
            $this->redirectBack();
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

        $deleted = 0;
        $failed  = [];

        // Best effort: each item fails on its own, the loop keeps going.
        foreach ($selected_ids as $id) {
            try {
                $item = $this->service->getById($id);

                if (! $item) {
                    $failed[] = sprintf(__('Item #%d was not found.', 'woo-extender'), $id);
                    continue;
                }

                if (! $this->service->delete($item)) {
                    throw new \RuntimeException("delete() returned false for item #{$id}");
                }

                $deleted++;
            } catch (BusinessRuleException $e) {
                // Expected: the reason is meant for the user.
                $failed[] = $e->getMessage();
            } catch (\Throwable $e) {
                // Unexpected: details go to the log, the user gets a generic message.
                error_log('[woo-extender] ' . $e->getMessage());
                $failed[] = sprintf(__('Item #%d could not be deleted due to an unexpected error.', 'woo-extender'), $id);
            }
        }

        if ($deleted > 0) {
            $this->flashNotices->add('success', sprintf(_n('%d item deleted.', '%d items deleted.', $deleted, 'woo-extender'), $deleted));
        }

        if (! empty($failed)) {
            $this->flashNotices->add('warning', $failed);
        }

        $this->redirect(empty($failed) ? 'deleted' : 'error');
    }

    protected function redirect(string $message): void
    {
        $page_slug = 'woo-extender-' . $this->getPage()->value;
        wp_safe_redirect(admin_url("admin.php?page={$page_slug}&message={$message}"));
        exit;
    }

    /**
     * Back to the page the form was submitted from (the form itself), or the list if unknown.
     */
    protected function redirectBack(): void
    {
        $referer = wp_get_referer();

        if (! $referer) {
            $this->redirect('error');
        }

        wp_safe_redirect($referer);
        exit;
    }
}