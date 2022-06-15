<?php

namespace MetForm_Pro\Core\Integrations\Auth\Register;

use MetForm_Pro\Traits\Singleton;
use MetForm_Pro\Utils\Render;

defined('ABSPATH') || exit;

class Loader
{
    use Singleton;


    public function init()
    {

        $loader = \MetForm_Pro\Core\Integrations\Auth\Loader\Loader::instance();
        $parent_id = $loader->id;
        add_action('mf_push_tab_content_' . $parent_id, [$this, 'settings_content']);
        add_action('mf_cpt', function () {

            return [
                'mf_registration' => [
                    'name' => 'mf_registration',
                ],
            ];
        });


        add_action('rest_api_init', function () {

            register_rest_route('xs/register', '/settings/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [$this, 'rest_func'],
                'permission_callback' => '__return_true',
            ]);

        });

        add_action('rest_api_init', function () {

            register_rest_route('xs/register', '/test', [
                'methods' => 'GET',
                'callback' => [$this, 'test_rest'],
                'permission_callback' => '__return_true',
            ]);

            register_rest_route('xs/register', '/settings/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [$this, 'rest_func'],
                'permission_callback' => '__return_true',
            ]);

        });
    }


    public function rest_func($request)
    {
        $id = $request['id'];

        return get_option('mf_auth_reg_settings_' . $id);
    }


    public function test_rest()
    {
        $id = 5;

        return get_option('mf_auth_reg_settings_' . $id);
    }


    public function settings_content()
    {
        $data = [
            'name' => 'mf_registration',
            'label' => 'Registration',
            'class' => 'mf-register',
            'details' => 'Enable or disable user registration',
        ];

        Render::checkbox($data);
        Render::div('', 'mf_register_form_fields');


    }
}

Loader::instance()->init();

