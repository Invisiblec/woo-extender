<?php

declare(strict_types=1);

namespace WooExtender\Models;

defined('ABSPATH') || exit;

class WarrantyModel extends BaseModel
{
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
}
