<?php

use MetForm_Pro\Utils\Helper;

defined('ABSPATH') || exit;

/**
 * Plugin Name: MetForm Pro
 * Plugin URI:  http://products.wpmet.com/metform/
 * Description: Most flexible and design friendly form builder for Elementor
 * Version: 1.3.3
 * Author: Wpmet
 * Author URI:  https://wpmet.com
 * Text Domain: metform-pro
 * Domain Path: /languages
 * License:  GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

require_once plugin_dir_path( __FILE__ ) . '/autoloader.php';
require_once plugin_dir_path( __FILE__ ) . '/libs/vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . '/plugin.php';
require_once plugin_dir_path( __FILE__ ) . '/utils/notice/notice.php';

\Oxaim\Libs\Notice::init();

add_action('plugins_loaded', function () {

    MetForm_Pro\Plugin::instance()->init();

	/**
     * ---------------------------------------------------------
     * Woocommerce Checkout feature
     * Add metform entires into woocommerce cart as a product
     * Price will come from the calculation form
     * ---------------------------------------------------------
     *
     * Add functionality to @wp_head hook
     */

    require_once plugin_dir_path( __FILE__ ) .  '/core/integrations/ecommerce/woocommerce/woo-cpt.php';

    do_action('xpd_metform_pro/plugin_loaded');

}, 115);
