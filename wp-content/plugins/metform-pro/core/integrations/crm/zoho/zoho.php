<?php

/**
 * Zoho CRM integration .
 * This integration allow you to save your
 * contast form metform to your zoho contact section
 *
 */

namespace MetForm_Pro\Core\Integrations\Crm\Zoho;

defined('ABSPATH') || exit;

class Zoho
{

    public function create_contact($form_data, $settings)
    {

        $first_name = (isset($form_data['mf-listing-fname']) ? $form_data['mf-listing-fname'] : '');
        $last_name = (isset($form_data['mf-listing-lname']) ? $form_data['mf-listing-lname'] : '');
        $email = (isset($form_data[$settings['email_name']]) ? $form_data[$settings['email_name']] : '');

        $settings_option = \MetForm\Core\Admin\Base::instance()->get_settings_option();

        $token = $settings_option['mf_zoho_token'];

        $url = 'https://www.zohoapis.com/crm/v2/Contacts';
        $data = [
            'data' => [
                [
                    'Last_Name' => $first_name,
                    'First_Name' => $last_name,
                    'Email' => $email,
                ],
            ],
        ];



        $response = wp_remote_post($url, [
            'method' => 'POST',
            'timeout' => 45,
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $token,
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            'body' => json_encode($data),
        ]);
        

      

    }

}
