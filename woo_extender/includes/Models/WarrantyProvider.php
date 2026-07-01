<?php

namespace WooExtender\Models;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class WarrantyProvider extends BaseModel
{

    protected static string $table_name = 'woo_extndr_warranty_providers';
    protected static array $searchable_columns = ['name', 'slug'];
    protected static array $allowed_orderby = ['name', 'slug', 'created_at'];

    protected static function get_child_schema_fields(): string
    {
        $query =
            "name VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            support_phone VARCHAR(50) NULL,
            email VARCHAR(190) NULL,
            website VARCHAR(255) NULL,
            warranty_default_months INT NULL,
            notes TEXT NULL,";

        return $query;
    }

    protected static function get_child_schema_indexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    public static function get_form_fields(): array
    {
        return [
            'name' => [
                'label'    => __('Warranty Provider Name', 'woo-extender'),
                'type'     => 'text',
                'required' => true
            ],
            'slug' => [
                'label'    => __('Slug', 'woo-extender'),
                'type'     => 'text',
                'required' => true
            ],
            'phone' => [
                'label'    => __('Phone', 'woo-extender'),
                'type'     => 'tel',
                'required' => false
            ],
            'support_phone' => [
                'label'    => __('Support Phone', 'woo-extender'),
                'type'     => 'text',
                'required' => false
            ],
            'email' => [
                'label'    => __('Email', 'woo-extender'),
                'type'     => 'email',
                'required' => false
            ],
            'website' => [
                'label'    => __('Website', 'woo-extender'),
                'type'     => 'url',
                'required' => false
            ],
            'warranty_default_months' => [
                'label'    => __('Warranty Default Period (Months)', 'woo-extender'),
                'type'     => 'number',
                'required' => false
            ],
            'notes' => [
                'label'    => __('Notes', 'woo-extender'),
                'type'     => 'textarea',
                'required' => false
            ],
        ];
    }

    protected function is_valid_for_save(): bool
    {
        return ! empty($this->name);
    }

    protected function sanitize_child_fields(): array
    {

        $prepared = [];

        if (isset($this->phone))                    $prepared['phone'] = Sanitize::string($this->phone);
        if (isset($this->support_phone))            $prepared['support_phone'] = Sanitize::string($this->support_phone);
        if (isset($this->email))                    $prepared['email'] = Sanitize::email($this->email);
        if (isset($this->website))                  $prepared['website'] = Sanitize::url($this->website);
        if (isset($this->warranty_default_months))  $prepared['warranty_default_months'] = Sanitize::int($this->warranty_default_months);
        if (isset($this->notes))                    $prepared['notes'] = Sanitize::textarea($this->notes);

        return $prepared;
    }
}
