<?php

namespace MetForm_Pro\Core\Integrations\Crm\Hubspot;

use Exception;

defined('ABSPATH') || exit;

class Hubspot
{
    public function create_contact($form_data, $settings)
    {
        $settings_option = \MetForm\Core\Admin\Base::instance()->get_settings_option();

        $arr = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => (isset($form_data[$settings['email_name']]) ? $form_data[$settings['email_name']] : ''),
                ),
                array(
                    'property' => 'firstname',
                    'value' => (isset($form_data['mf-listing-fname']) ? $form_data['mf-listing-fname'] : ''),
                ),
                array(
                    'property' => 'lastname',
                    'value' => (isset($form_data['mf-listing-lname']) ? $form_data['mf-listing-lname'] : ''),
                ),
                array(
                    'property' => 'phone',
                    'value' => (isset($form_data['mf-listing-phone']) ? $form_data['mf-listing-phone'] : ''),
                ),
            ),
        );
        $post_json = json_encode($arr);
        $hapikey = $settings_option['mf_hubsopt_token'];
        $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $hapikey;
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        @curl_setopt($ch, CURLOPT_URL, $endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        @curl_close($ch);
    }

    public function submit_data($form_id, $form_data, $settings)
    {
        $portal_id = get_option('mf_hubspot_form_portalId_'.$form_id);
        $gu_id = get_option('mf_hubspot_form_guid_'.$form_id);

      

        $dd = get_option('mf_hubspot_form_data_' . $form_id);

        $data = [];

        foreach($dd as $d){
            foreach($d as $key => $value){
                $k = str_replace('mf_hubspot_form_field_name_','',$key);
                array_push($data,[
                    
                    'name' => $k,
                    'value' => $form_data[$value]

                ]);
            }
        }

        $api_url = 'https://api.hsforms.com/submissions/v3/integration/submit/' . $portal_id . '/' . $gu_id;


        $body = json_encode(['fields' => $data]);

        try{

            $response = wp_remote_post(
                $api_url,
                [
                    'method' => 'POST',
                    'data_format' => 'body',
                    'timeout' => 45,
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                    ],
                    'body' => $body,
                ]
            );

            

            file_put_contents('debug.json',json_encode($response));

        }catch(Exception $e){

            $myfile = fopen("debug.txt", "w");
            $txt = $e;
            fwrite($myfile, $txt);
            fclose($myfile);
        }

       

    }
}
