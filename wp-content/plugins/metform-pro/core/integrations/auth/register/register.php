<?php

/**
 * Registration system inegration
 * for metform pro
 *
 */

namespace MetForm_Pro\Core\Integrations\Auth\Register;

defined('ABSPATH') || exit;

class Register
{
    /** Do registration Action */

    public function action($form_id, $form_data)
    {

        /** Get form settings data */

        $settings = get_option('mf_auth_reg_settings_' . $form_id);

        $user_name = $form_data[$settings['mf_auth_reg_user_name']];
        $user_email = $form_data[$settings['mf_auth_reg_user_email']];
        $user_role = $settings['mf_auth_reg_role'];
        $user_password = rand(100000, 999999);

        $userdata = array(
            'user_login' => $user_name,
            'user_email' => $user_email,
            'user_pass' => $user_password,
            'role' => $user_role,
        );

        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {
            // Email login details to user
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            $message = "Welcome! Your login details are as follows:" . "\r\n";
            $message .= sprintf(__('Username: %s', 'metform-pro'), $user_name) . "\r\n";
            $message .= sprintf(__('Password: %s', 'metform-pro'), $user_password) . "\r\n";
            $message .= wp_login_url() . "\r\n";
            wp_mail($user_email, sprintf(__('[%s] Your username and password', 'metform-pro'), $blogname), $message);
        }

    }

}
