<?php

namespace WooExtender\Models;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

abstract class BaseModel
{
    protected static string $table_name;
    protected static array $searchable_columns;
    protected static string $display_column = 'name';
    protected static array $allowed_orderby = ['id', 'created_at'];

    abstract protected static function get_child_schema_fields(): string;
    abstract protected static function get_child_schema_indexes(): string;

    abstract protected function sanitize_child_fields(): array;
    abstract protected function is_valid_for_save(): bool;

    public function __construct(protected array $attr = []) {}

    public function __get(string $key)
    {
        return $this->attr[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attr[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->attr[$key]);
    }

    protected static function get_table_name(): string
    {
        global $wpdb;
        return $wpdb->prefix . static::$table_name;
    }

    public static function get_table_schema(): string
    {
        global $wpdb;
        $table_name = static::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $child_fields = trim(static::get_child_schema_fields());
        $child_fields = rtrim($child_fields, ',');

        $query = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            $child_fields,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id)";

        $indexes = static::get_child_schema_indexes();

        if (! empty($indexes)) {
            $query .= ", " . rtrim($indexes, ',');
        }

        $query .= "\n) {$charset_collate};";

        return $query;
    }

    public static function get_by_id(int $id): ?static
    {
        global $wpdb;
        $table_name = static::get_table_name();
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);

        if (! $row) return null;

        return new static($row);
    }

    public static function get_all(array $args = []): array
    {
        global $wpdb;
        $table_name = static::get_table_name();
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

        if (! empty($args['search']) && ! empty(static::$searchable_columns)) {

            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach (static::$searchable_columns as $column) {
                $search_conditions[] = "{$column} LIKE %s";
                $query_params[] = $search_term;
            }

            $sql .= " AND (" . implode(' OR ', $search_conditions) . ")";
        }


        if ($args['status'] !== null) {
            $sql .= " AND status = %d";
            $query_params[] = (int) $args['status'];
        }

        $orderby = in_array($args['orderby'], static::$allowed_orderby, true) ? $args['orderby'] : 'id';
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

        return array_map(function ($row) {
            return new static($row);
        }, $results);
    }

    public static function get_count(array $args = []): string
    {
        global $wpdb;
        $table_name = static::get_table_name();

        $args = wp_parse_args($args, [
            'search' => '',
            'status' => null
        ]);

        $sql = "SELECT COUNT(id) FROM $table_name WHERE 1=1";
        $query_params = [];

        if (! empty($args['search']) && ! empty(static::$searchable_columns)) {
            $search_term = "%" . $wpdb->esc_like($args['search']) . "%";
            $search_conditions = [];

            foreach (static::$searchable_columns as $column) {
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

    public static function get_as_options_list(): ?array
    {
        global $wpdb;
        $table_name = static::get_table_name();
        $display_col = static::$display_column;

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

    public function save(): int|bool
    {
        global $wpdb;

        if (! $this->is_valid_for_save()) {
            return false;
        }

        $table_name     = static::get_table_name();
        $sanitized_data = $this->sanitized_and_prepare();

        $id = isset($this->attr['id']) ? (int) $this->attr['id'] : 0;
        unset($sanitized_data['id']);


        if ($id > 0) {
            $sanitized_data['updated_at'] = current_time('mysql');

            $updated = $wpdb->update(
                $table_name,
                $sanitized_data,
                ['id' => $id]
            );

            if ($updated !== false) {
                $this->updated_at = $sanitized_data['updated_at'];
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
            $this->id = (int) $wpdb->insert_id;
            $this->created_at = $sanitized_data['created_at'];
            $this->status = $sanitized_data['status'];
            return $this->id;
        }

        return false;
    }

    public function delete(): bool
    {
        global $wpdb;

        if (empty($this->id)) return false;

        $table_name   = static::get_table_name();
        $current_time = current_time('mysql');

        $data_to_update = [
            'status' => 0,
            'updated_at' => $current_time
        ];

        $where_conditions = [
            'id' => $this->id
        ];

        $result = $wpdb->update(
            $table_name,
            $data_to_update,
            $where_conditions
        );

        if ($result !== false) {
            $this->status = 0;
            $this->updated_at = $current_time;
            return true;
        }

        return false;
    }

    protected function sanitized_and_prepare(): array
    {
        $prepared = [];

        if (isset($this->slug) || isset($this->name)) {

            if (empty($this->slug) && ! empty($this->name)) {
                $prepared['slug'] = sanitize_title($this->name);
            } else {
                $prepared['slug'] = sanitize_title($this->slug);
            }

            if (isset($this->name)) $prepared['name'] = Sanitize::string($this->name);
        }

        if (isset($this->status)) $prepared['status'] = (int) $this->status;

        $child_data = $this->sanitize_child_fields();

        return array_merge($prepared, $child_data);
    }
}
