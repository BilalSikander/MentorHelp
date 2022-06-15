<?php

namespace MetForm_Pro\Core\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * License related all functionalities.
 *
 * @version 1.6.18
 */
class Base {
    use \MetForm\Traits\Singleton;

    public static function parent_slug(){
        return 'metform-menu';
    }

    public function init(){
        $this->auto_updater();
        add_action('admin_menu', [$this, 'register_menus'], 999);
        add_action('admin_init', [$this, 'register_actions'], 999);
    }

    public function register_menus(){
        add_submenu_page( self::parent_slug(), esc_html__( 'License', 'metform-pro' ), esc_html__( 'License', 'metform-pro' ), 'manage_options', self::parent_slug().'-license', [$this, 'register_settings_contents__license'], 11);
    }

    public function register_settings_contents__license(){
        include('views/license.php');
    }

    public function register_actions(){
        if(isset( $_POST['metform-pro-settings-page-action'])) {
            // run a quick security check
            $key = !isset($_POST['metform-pro-settings-page-key']) ? '' : sanitize_text_field($_POST['metform-pro-settings-page-key']);

            if( !check_admin_referer('metform-pro-settings-page', 'metform-pro-settings-page')){
                return;
            }
            

            switch($_POST['metform-pro-settings-page-action']){
                case 'activate':
                    \MetForm_Pro\Libs\License::instance()->activate($key);
                break;
                case 'deactivate':
                    \MetForm_Pro\Libs\License::instance()->deactivate();
                break;
            }
        }
    }

    public function auto_updater(){
        $license_key = trim(\MetForm_Pro\Libs\License::instance()->get_license());
        $plugin_dir_and_filename = \MetForm_Pro\Plugin::instance()->plugin_dir() . 'metform-pro.php';

        $active_plugins = get_option( 'active_plugins' );
        foreach ( $active_plugins as $active_plugin ) {
            if ( false !== strpos( $active_plugin, 'metform-pro.php' ) ) {
                $plugin_dir_and_filename = $active_plugin;
                break;
            }
        }
        if (!isset( $plugin_dir_and_filename ) || empty( $plugin_dir_and_filename)) {
            throw( 'Plugin not found! Check the name of your plugin file in the if check above' );
        }

        new \MetForm_Pro\Libs\Plugin_Updater(
            \MetForm_Pro\Plugin::instance()->account_url(),
            $plugin_dir_and_filename,
            array(
                'version' => \MetForm_Pro\Plugin::instance()->version(), // current version number.
                'license' => $license_key, // license key 
                'item_id' => \MetForm_Pro\Plugin::instance()->product_id(), // id of this product
                'author'  => \MetForm_Pro\Plugin::instance()->author_name(), // author of this plugin.
                'url'     => home_url(),
            )
        );
    }
}