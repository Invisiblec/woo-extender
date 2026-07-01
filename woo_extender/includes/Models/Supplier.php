<?php

namespace WooExtender\Models;

use WooExtender\Helpers\Sanitize;

defined('ABSPATH') || exit;

class Supplier extends BaseModel
{
    protected static string $table_name = 'woo_extndr_suppliers';
    protected static array $searchable_columns = ['name', 'slug'];
    protected static array $allowed_orderby = ['name', 'slug', 'created_at'];

    protected static function get_child_schema_fields(): string
    {
        return
            "name VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            sales_person VARCHAR(190) NULL,
            sales_phone VARCHAR(50) NULL,
            email VARCHAR(190) NULL,
            website VARCHAR(255) NULL,
            address TEXT NULL,
            notes TEXT NULL,";
    }

    protected static function get_child_schema_indexes(): string
    {
        return "UNIQUE KEY slug (slug)";
    }

    public static function get_form_fields(): array
    {
        return [
            'name' => [
                'label'    => __('Supplier Name', 'woo-extender'),
                'type'     => 'text',
                'required' => true,
            ],
            'slug' => [
                'label'    => __('Slug', 'woo-extender'),
                'type'     => 'text',
                'required' => true,
            ],
            'phone' => [
                'label'    => __('Phone Number', 'woo-extender'),
                'type'     => 'tel',
                'required' => false,
            ],
            'sales_person' => [
                'label'    => __('Sales Contact', 'woo-extender'),
                'type'     => 'text',
                'required' => false,
            ],
            'sales_phone' => [
                'label'    => __('Sales Phone', 'woo-extender'),
                'type'     => 'tel',
                'required' => false,
            ],
            'email' => [
                'label'    => __('Email Address', 'woo-extender'),
                'type'     => 'email',
                'required' => false,
            ],
            'website' => [
                'label'    => __('Website', 'woo-extender'),
                'type'     => 'url',
                'required' => false,
            ],
            'address' => [
                'label'    => __('Address', 'woo-extender'),
                'type'     => 'textarea',
                'required' => false,
            ],
            'notes' => [
                'label'    => __('Notes', 'woo-extender'),
                'type'     => 'textarea',
                'required' => false,
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

        if (isset($this->phone))        $prepared['phone'] = Sanitize::string($this->phone);
        if (isset($this->sales_person)) $prepared['sales_person'] = Sanitize::string($this->sales_person);
        if (isset($this->sales_phone))  $prepared['sales_phone'] = Sanitize::string($this->sales_phone);
        if (isset($this->email))        $prepared['email'] = Sanitize::email($this->email);
        if (isset($this->website))      $prepared['website'] = Sanitize::url($this->website);
        if (isset($this->address))      $prepared['address'] = Sanitize::textarea($this->address);
        if (isset($this->notes))        $prepared['notes'] = Sanitize::textarea($this->notes);

        return $prepared;
    }
}
