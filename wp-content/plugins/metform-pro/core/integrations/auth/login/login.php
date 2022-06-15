<?php

/**
 * Login system integratioin for metform pro
 * 
 */

namespace MetForm_Pro\Core\Integrations\Auth\Login;

defined('ABSPATH') || exit;
 
class Login{

    public function action($form_id,$form_data){
        $settings = get_option('mf_auth_login_settings_'.$form_id);

        $user_name = $form_data[$settings['mf_auth_login_user_name']];
        $user_password = $form_data[$settings['mf_auth_login_user_password']];

        $creds = array(
            'user_login'    => $user_name,
            'user_password' => $user_password,
            'remember'      => true
        );
     
       wp_signon( $creds, false );
     
      
    }


 }



