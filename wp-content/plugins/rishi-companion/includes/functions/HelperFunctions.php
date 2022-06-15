<?php
/**
 * Helper Functions.
 *
 * @package Rishi_Companion
 */


function rishi_companion_call_fn($args = [], ...$params) {
	$args = wp_parse_args(
		$args,
		[
			'fn' => null,

			// string | null | array
			'default' => ''
		]
	);

	if (! $args['fn']) {
		throw new Error('$fn must be specified!');
	}

	if (! function_exists($args['fn'])) {
		return $args['default'];
	}

	return call_user_func($args['fn'], ...$params);
}

/**
 * check if the current request is rest or ajax
 */
function rishi_companion_is_dynamic_request () {
    if((defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_is_json_request') && wp_is_json_request()) || wp_doing_ajax() || wp_doing_cron()) {
        return true;
    }

    return false;
}

/**
 * check for page builder query args
 *
 * @return void
 */
function rishi_companion_is_page_builder() {
	$page_builders = array(
		'elementor-preview', //elementor
		'fl_builder', //beaver builder
		'et_fb', //divi
		'ct_builder', //oxygen
		'tve' //thrive
	);

	foreach($page_builders as $page_builder) {
		if(isset($_GET[$page_builder])) {
			return true;
		}
	}

	return false;
}

/**
 * check for lazy load
 *
 * @return void
 */
function rishi_lazyload_get_atts_array($atts_string) {
	
	if(!empty($atts_string)) {
		$atts_array = array_map(
			function(array $attribute) {
				return $attribute['value'];
			},
			wp_kses_hair($atts_string, wp_allowed_protocols())
		);

		return $atts_array;
	}

	return false;
}

/**
 * check for lazy load
 *
 * @return void
 */
function rishi_lazyload_get_atts_string($atts_array) {

	if(!empty($atts_array)) {
		$assigned_atts_array = array_map(
		function($name, $value) {
				if($value === '') {
					return $name;
				}
				return sprintf('%s="%s"', $name, esc_attr($value));
			},
			array_keys($atts_array),
			$atts_array
		);
		$atts_string = implode(' ', $assigned_atts_array);

		return $atts_string;
	}

	return false;
}

/**
 * check for comment counts
 *
 * @return void
 */
function rishi_companion_get_comments_count() {

	global $post;

	return get_comments_number($post);
}