<?php

namespace MetForm_Pro\Core\Integrations\Auth\Login;

use MetForm_Pro\Traits\Singleton;
use MetForm_Pro\Utils\Render;

defined('ABSPATH') || exit;

class Loader {
	use Singleton;


	public function init() {
		$loader    = \MetForm_Pro\Core\Integrations\Auth\Loader\Loader::instance();
		$parent_id = $loader->id;
		add_action('mf_push_tab_content_' . $parent_id, [$this, 'settings_content']);

		add_action('rest_api_init', function() {

			register_rest_route('xs/login', '/settings/(?P<id>\d+)', [
				'methods'  => 'GET',
				'callback' => [$this, 'rest_func'],
				'permission_callback' => '__return_true',
			]);

		});

	}


	public function rest_func($request) {
		$id = $request['id'];

		return get_option('mf_auth_login_settings_' . $id);
	}


	public function settings_content() {
		$data = [
			'name'    => 'mf_login',
			'label'   => 'Login',
			'class'   => 'mf-login',
			'details' => 'Enable or Disable login system',
		];

		Render::checkbox($data);
		Render::div('', 'mf_login_form_fields');
		Render::seperator();
	}
}

Loader::instance()->init();

