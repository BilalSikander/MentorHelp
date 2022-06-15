<?php

namespace MetForm_Pro\Core\Integrations\Google_Sheet;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use GuzzleHttp\Client;
use MetForm\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

class WF_Google_Sheet {

    use Singleton;

    public $google_client_id;

	public $google_client_secret;

    public function __construct()
    {
        $settings = \MetForm\Core\Admin\Base::instance()->get_settings_option();
        $this->google_client_id = isset($settings['mf_google_sheet_client_id']) ? $settings['mf_google_sheet_client_id'] : '';
        $this->google_client_secret = isset($settings['mf_google_sheet_client_secret']) ? $settings['mf_google_sheet_client_secret'] : '';
    }

    public function create($title, $names) {
        $client = new Google_Client();

        $arr_token = get_option('wf_google_access_token');
        if($arr_token) {
            $arr_token = json_decode($arr_token);
        }else {
            return false;
        }
        $accessToken = array(
            'access_token' => $arr_token->access_token,
            'expires_in' => $arr_token->expires_in,
        );

        $client->setAccessToken($accessToken);
 
        $service = new Google_Service_Sheets($client);

        try {
            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => $title
                ]
            ]);
            $spreadsheet = $service->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);
            $this->insert_names($spreadsheet->spreadsheetId, $names);
            return $spreadsheet->spreadsheetId;

        }catch(\Exception $e) {
            return false;
        }

    }

    public function get_sheet_id($form_id, $names, $title){
        $google_sheet_id = 'wf_google_sheet_'.$form_id;
        $sheet = get_option($google_sheet_id);
        if($sheet){
            return $sheet;
        }else{
            $create = $this->create($title, $names);
            if ($create) {
                add_option($google_sheet_id, $create);
                return $create;
            }
            return false;
        }
    }

    public function update_names($form_id, $form_fields) {
        $google_sheet_option_name = 'wf_google_sheet_'.$form_id.'names';
        $option_names = get_option($google_sheet_option_name);
        if($option_names) {
            foreach($option_names as $option) {
                unset($form_fields[$option]);
            }
            foreach($form_fields as $key => $field) {
                $option_names[] = $key;
            }
        }else {
            foreach($form_fields as $key => $form_fields) {
                $option_names[] = $key;
            }
        }
        update_option($google_sheet_option_name, $option_names);

        return $option_names;
        
    }

    public function insert($form_id, $form_title, $form_data, $file_upload_info, $form_fields) {

        $google_sheet_option_name = 'wf_google_sheet_'.$form_id.'names';
        $names = get_option($google_sheet_option_name);
        $values = [];
        if(!$names) {
            $names = $this->update_names($form_id, $form_fields);
        }
        if(isset($file_upload_info)){
            foreach($file_upload_info as $key => $file) {
                $form_data[$key] = isset($file['url']) ? $file['url'] : '';
            }
        }

        foreach($names as $name) {
            $values[] = isset($form_data[$name]) ? $form_data[$name] : '';
        }
        $sheet_id = $this->get_sheet_id($form_id, $names, $form_title);

        if($sheet_id === false) {
            return false;
        } 

        $service = $this->service();
        if($service == false) {
            return false;
        }
        try {
            $range = 'A1:Z1';
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$values]
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $result = $service->spreadsheets_values->append($sheet_id, $range, $body, $params);
        } catch(\Exception $e) {
            $arr_token = $this->token();
            if($arr_token == false) {
                return false;
            }
            if( 401 == $e->getCode() ) {
                $client = new Client(['base_uri' => 'https://accounts.google.com']);
  
                $response = $client->request('POST', '/o/oauth2/token', [
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $arr_token->refresh_token,
                        "client_id" => $this->google_client_id,
                        "client_secret" => $this->google_client_secret,
                    ],
                ]);
    
                $data = (array) json_decode($response->getBody());
                $data['refresh_token'] = $arr_token->refresh_token;
                update_option('wf_google_access_token', json_encode($data));
                $this->insert($form_id, $form_title, $form_data, $file_upload_info, $form_fields);
            }
        }
    }

    public function insert_names($sheet_id, $names){
        $service = $this->service();
        if($service == false) {
            return false;
        }
        try {
            $range = 'A1:Z1';
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$names]
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $result = $service->spreadsheets_values->update($sheet_id, $range, $body, $params);
        } catch(\Exception $e) {
            $arr_token = $this->token();
            if($arr_token == false) {
                return false;
            }
            if( 401 == $e->getCode() ) {
                $client = new Client(['base_uri' => 'https://accounts.google.com']);
  
                $response = $client->request('POST', '/o/oauth2/token', [
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $arr_token->refresh_token,
                        "client_id" => $this->google_client_id,
                        "client_secret" => $this->google_client_secret,
                    ],
                ]);
    
                $data = (array) json_decode($response->getBody());
                $data['refresh_token'] = $arr_token->refresh_token;
                update_option('wf_google_access_token', json_encode($data));
                $this->insert_names($sheet_id, $names);
            }
        }
    }

    public function service() {

        $client = new Google_Client();
        $arr_token = $this->token();
        if($arr_token == false) {
            return false;
        }
        $accessToken = array(
            'access_token' => $arr_token->access_token,
            'expires_in' => $arr_token->expires_in,
        );
        $client->setAccessToken($accessToken);
        $service = new Google_Service_Sheets($client);
        return $service;
    }

    public function token() {
        $arr_token = get_option('wf_google_access_token');
        if($arr_token) {
            return json_decode($arr_token);
        }else {
            return false;
        }
    }
}