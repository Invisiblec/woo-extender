<?php

declare(strict_types=1);

namespace WooExtender\Repositories;

use WooExtender\Helpers\Sanitize;
use WooExtender\Models\BaseModel;

defined('ABSPATH') || exit;

abstract class BaseRepository
{
    protected string $table_name;
    protected array $searchable_columns;
    protected string $display_column = 'name';
    protected array $allowed_orderby = ['id', 'created_at'];

    abstract protected function get_child_schema_fields(): string;
    abstract protected function get_child_schema_indexes(): string;
    abstract protected function get_model_class(): string;

    abstract protected function sanitize_child_fields(BaseModel $model): array;
    abstract protected function is_valid_for_save(BaseModel $model): bool;

    public function get_table_schema(): string
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $child_fields = trim($this->get_child_schema_fields());
        $child_fields = rtrim($child_fields, ',');

        $query = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            $child_fields,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id)";

        $indexes = $this->get_child_schema_indexes();

        if (! empty($indexes)) {
            $query .= ", " . rtrim($indexes, ',');
        }

        $query .= "\n) {$charset_collate};";

        return $query;
    }

    protected function get_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }

    public function getById(int $id): ?BaseModel
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);

        if (! $row) return null;

        $model_class = $this->get_model_class();

        return new $model_class($row);
    }

    public function get_all(array $args = []): array
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $sql = "SELECT * FROM {$table_name} WHERE 1=1";

        $args = wp_parse_args($args, [
            'search'  => '',
            'status'  => null,
            'orderby' => 'id',
            'order'   => 'DESC',
            'limit'   => 20,
            'paged'   => 1
        ]);

        $query_params = [];

        if (! empty($args['search']) && ! empty($this->searchable_columns)) {

            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach ($this->searchable_columns as $column) {
                $search_conditions[] = "{$column} LIKE %s";
                $query_params[] = $search_term;
            }

            $sql .= " AND (" . implode(' OR ', $search_conditions) . ")";
        }


        if ($args['status'] !== null) {
            $sql .= " AND status = %d";
            $query_params[] = (int) $args['status'];
        }

        $orderby = in_array($args['orderby'], $this->allowed_orderby, true) ? $args['orderby'] : 'id';
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

        $model_class = $this->get_model_class();

        return array_map(function ($row) use ($model_class) {
            return new $model_class($row);
        }, $results);
    }

    public function get_count(array $args = []): string
    {
        global $wpdb;
        $table_name = $this->get_table_name();

        $args = wp_parse_args($args, [
            'search' => '',
            'status' => null
        ]);

        $sql = "SELECT COUNT(id) FROM $table_name WHERE 1=1";
        $query_params = [];

        if (! empty($args['search']) && ! empty($this->searchable_columns)) {
            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach ($this->searchable_columns as $column) {
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

    public function get_as_options_list(): ?array
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $display_col = $this->display_column;

        $sql = $wpdb->prepare(
            "SELECT id, {$display_col} AS display_name FROM {$table_name} WHERE status = %d ORDER BY {$display_col} ASC",
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

        if (! $this->is_valid_for_save($model)) {
            return false;
        }

        $table_name     = $this->get_table_name();
        $sanitized_data = $this->sanitized_and_prepare($model);

        $id = isset($model->id) ? (int) $model->id : 0;
        unset($sanitized_data['id']);


        if ($id > 0) {
            $sanitized_data['updated_at'] = current_time('mysql');

            $updated = $wpdb->update(
                $table_name,
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
        $result = $wpdb->insert($table_name, $sanitized_data);

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

        $table_name   = $this->get_table_name();
        $current_time = current_time('mysql');

        $data_to_update = [
            'status' => 0,
            'updated_at' => $current_time
        ];

        $where_conditions = [
            'id' => $model->id
        ];

        $result = $wpdb->update(
            $table_name,
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

    protected function sanitized_and_prepare(BaseModel $model): array
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

        $child_data = $this->sanitize_child_fields($model);

        return array_merge($prepared, $child_data);
    }
}
