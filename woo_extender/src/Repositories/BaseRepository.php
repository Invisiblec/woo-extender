<?php

declare(strict_types=1);

namespace WooExtender\Repositories;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;

defined('ABSPATH') || exit;

abstract class BaseRepository
{
    protected string $tableName;
    protected array $searchableColumns;
    protected string $displayColumn = 'name';
    protected array $allowedOrderby = ['id', 'created_at'];

    abstract protected function getChildSchemaFields(): string;
    abstract protected function getChildSchemaIndexes(): string;
    abstract protected function getModelClass(): string;

    abstract protected function sanitizeChildFields(BaseModel $model): array;
    abstract protected function isValidForSave(BaseModel $model): bool;

    public function getTableSchema(): string
    {
        global $wpdb;
        $tableName = $this->getTableName();
        $charset_collate = $wpdb->get_charset_collate();

        $child_fields = trim($this->getChildSchemaFields());
        $child_fields = rtrim($child_fields, ',');

        $query = "CREATE TABLE $tableName (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            $child_fields,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id)";

        $indexes = $this->getChildSchemaIndexes();

        if (! empty($indexes)) {
            $query .= ", " . rtrim($indexes, ',');
        }

        $query .= "\n) {$charset_collate};";

        return $query;
    }

    protected function getTableName(): string
    {
        global $wpdb;
        return $wpdb->prefix . $this->tableName;
    }

    public function getById(int $id): ?BaseModel
    {
        global $wpdb;
        $tableName = $this->getTableName();
        $sql = $wpdb->prepare("SELECT * FROM $tableName WHERE id = %d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);

        if (! $row) return null;

        $model_class = $this->getModelClass();

        return new $model_class($row);
    }

    public function getAll(array $args = []): array
    {
        global $wpdb;
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE 1=1";

        $args = wp_parse_args($args, [
            'search'  => '',
            'status'  => null,
            'orderby' => 'id',
            'order'   => 'DESC',
            'limit'   => 20,
            'paged'   => 1
        ]);

        $query_params = [];

        if (! empty($args['search']) && ! empty($this->searchableColumns)) {

            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach ($this->searchableColumns as $column) {
                $search_conditions[] = "{$column} LIKE %s";
                $query_params[] = $search_term;
            }

            $sql .= " AND (" . implode(' OR ', $search_conditions) . ")";
        }


        if ($args['status'] !== null) {
            $sql .= " AND status = %d";
            $query_params[] = (int) $args['status'];
        }

        $orderby = in_array($args['orderby'], $this->allowedOrderby, true) ? $args['orderby'] : 'id';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY {$orderby} {$order}";

        $limit = (int) $args['limit'];
        $paged = max(1, $args['paged']);
        $offset = ($paged - 1) * $limit;

        $sql .= " LIMIT %d OFFSET %d";
        $query_params[] = $limit;
        $query_params[] = $offset;

        $sql = $wpdb->prepare($sql, ...$query_params);
        $results = $wpdb->get_results($sql, ARRAY_A);

        if (! is_array($results)) return [];

        $model_class = $this->getModelClass();

        return array_map(function ($row) use ($model_class) {
            return new $model_class($row);
        }, $results);
    }

    public function getCount(array $args = []): string
    {
        global $wpdb;
        $tableName = $this->getTableName();

        $args = wp_parse_args($args, [
            'search' => '',
            'status' => null
        ]);

        $sql = "SELECT COUNT(id) FROM $tableName WHERE 1=1";
        $query_params = [];

        if (! empty($args['search']) && ! empty($this->searchableColumns)) {
            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach ($this->searchableColumns as $column) {
                $search_conditions[] = "{$column} LIKE %s";
                $query_params[] = $search_term;
            }

            $sql .= " AND (" . implode(' OR ', $search_conditions) . ")";
        }

        if ($args['status'] !== null) {
            $sql .= " AND status = %d";
            $query_params[] = (int) $args['status'];
        }
        $sql = $wpdb->prepare($sql, ...$query_params);

        $total_items = $wpdb->get_var($sql);

        return $total_items;
    }

    public function getAsOptionsList(): ?array
    {
        global $wpdb;
        $tableName = $this->getTableName();
        $display_col = $this->displayColumn;

        $sql = $wpdb->prepare(
            "SELECT id, {$display_col} AS display_name FROM {$tableName} WHERE status = %d ORDER BY {$display_col} ASC",
            1
        );

        $results = $wpdb->get_results($sql, ARRAY_A);

        if (! $results) return null;

        $options = [];

        foreach ($results as $row) {
            $options[(int) $row['id']] = wp_strip_all_tags($row['display_name']);
        }

        return $options;
    }

    public function save(BaseModel $model): int|bool
    {
        global $wpdb;

        if (! $this->isValidForSave($model)) {
            return false;
        }

        $tableName     = $this->getTableName();
        $sanitized_data = $this->sanitizedAndPrepare($model);

        $id = isset($model->id) ? (int) $model->id : 0;
        unset($sanitized_data['id']);


        if ($id > 0) {
            $sanitized_data['updated_at'] = current_time('mysql');

            $updated = $wpdb->update(
                $tableName,
                $sanitized_data,
                ['id' => $id]
            );

            if ($updated !== false) {
                $model->updated_at = $sanitized_data['updated_at'];
                return $id;
            }

            return false;
        }

        if (! isset($sanitized_data['status'])) {
            $sanitized_data['status'] = 1;
        }

        $sanitized_data['created_at'] = current_time('mysql');
        $result = $wpdb->insert($tableName, $sanitized_data);

        if ($result !== false) {
            $model->id = (int) $wpdb->insert_id;
            $model->created_at = $sanitized_data['created_at'];
            $model->status = $sanitized_data['status'];
            return $model->id;
        }

        return false;
    }

    public function delete(BaseModel $model): bool
    {
        global $wpdb;

        if (empty($model->id)) return false;

        $tableName   = $this->getTableName();
        $current_time = current_time('mysql');

        $data_to_update = [
            'status' => 0,
            'updated_at' => $current_time
        ];

        $where_conditions = [
            'id' => $model->id
        ];

        $result = $wpdb->update(
            $tableName,
            $data_to_update,
            $where_conditions
        );

        if ($result !== false) {
            $model->status = 0;
            $model->updated_at = $current_time;
            return true;
        }

        return false;
    }

    protected function sanitizedAndPrepare(BaseModel $model): array
    {
        $prepared = [];

        if (isset($model->slug) || isset($model->name)) {

            if (empty($model->slug) && ! empty($model->name)) {
                $prepared['slug'] = sanitize_title($model->name);
            } else {
                $prepared['slug'] = sanitize_title($model->slug);
            }

            if (isset($model->name)) $prepared['name'] = Sanitize::string($model->name);
        }

        if (isset($model->status)) $prepared['status'] = (int) $model->status;

        $child_data = $this->sanitizeChildFields($model);

        return array_merge($prepared, $child_data);
    }
}