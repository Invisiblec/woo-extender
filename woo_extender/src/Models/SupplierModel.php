<?php

declare(strict_types=1);

namespace WooExtender\Models;

defined('ABSPATH') || exit;

class SupplierModel extends BaseModel
{
    public static function getFormFields(): array
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
}
