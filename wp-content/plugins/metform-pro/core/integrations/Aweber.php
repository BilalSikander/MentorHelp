<?php

namespace MetForm_Pro\Core\Integrations;


use MetForm_Pro\XPD_Constants;

class Aweber {

	const ACCESS_TOKEN_KEY       = 'met_form_aweber_mail_access_token_key';
	const REFRESH_TOKEN_KEY      = 'met_form_aweber_mail_refresh_token_key';
	const AUTHORIZATION_CODE_KEY = 'met_form_aweber_mail_auth_code_key';
	const NONCE_VERIFICATION_KEY = 'met_form_aweber_mail_state_key';
	const BASIC_AUTH_64_CRED_KEY = 'met_form_aweber_basic_auth_64_key';

	const AWEBER_LISTS_CACHE_KEY = 'mf_aweber_lists_key';


	protected $auth_url          = 'https://auth.aweber.com/oauth2/';
	protected $authorization_url = 'https://auth.aweber.com/oauth2/authorize';
	protected $access_token_url  = 'https://auth.aweber.com/oauth2/token';

	protected $aweber_api_account_url  = 'https://api.aweber.com/1.0/accounts';

	private $tmp_uri = '/admin.php?page=metform-menu-settings';


	/**
	 * Aweber constructor.
	 *
	 * @param bool $loadActions
	 */
	public function __construct($loadActions = true) {

		$this->tmp_uri = get_admin_url() . 'admin.php?page=metform-menu-settings';

		if($loadActions) {
			#Registering Aweber authorization check route only
			add_action('wp_ajax_get_aweber_authorization_url', [$this, 'get_aweber_authorization_url']);
			add_action('wp_ajax_get_aweber_re_authorization_url', [$this, 'get_aweber_re_authorization_url']);
			add_action('wp_ajax_get_list_lists', [$this, 'get_list_lists']);
		}
	}


	/**
	 *
	 * @param $accountId
	 *
	 * @return string
	 */
	public function build_list_fetching_url($accountId) {

		return $this->aweber_api_account_url .'/'.$accountId .'/lists';
	}


	/**
	 * Get the list of aweber mail list
	 *
	 * @return mixed
	 */
	public function get_list_lists() {

		$accToken = $this->get_access_token();

		if($accToken['result'] === XPD_Constants::RETURN_NOT_OKAY) {

			if(!empty($accToken['action_need'])) {

				return wp_send_json_error([
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Developer did not authorized the application, please first authorize the app and then try again.',
				]);
			}

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => $accToken['msg'],
			]);
		}

		if(empty($accToken['token'])) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Access token could not be retrieved.',
			]);
		}

		$accessToken = $accToken['token'];

		try {

			$bearerAuth = 'Bearer  ' . $accessToken;

			$headers = array(
				'Authorization' => $bearerAuth,
				'Accept' 	=> 'application/json',
				'User-Agent' 	=> 'XPD-AWeber-get-account',
			);

			$body = [];

			$payLoad = array(
				'method' => 'GET',
				'headers' => $headers,
				'body' => $body
			);

			$url = $this->aweber_api_account_url;

			$response = wp_remote_post($url, $payLoad);

		} catch(\Exception $ex) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Could not retrieved list due to api call fail! - ['. $ex->getMessage().']',
			]);
		}

		if (is_wp_error($response)) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Could not retrieved account information due to - '. $response->get_error_message(),
			]);
		}

		$json = json_decode($response['body']);

		if(!empty($json)) {

			if(property_exists($json, 'error')) {

				return wp_send_json_error([
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Error returned while getting accounts. ['.$json->error.' :: '.$json->message.']',
				]);
			}

			if($json->total_size < 1) {

				return wp_send_json_error([
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'No accounts found of this user!.',
				]);
			}

			#Do a loop to get all the accounts of this user...

			$entries = $json->entries;
			$account = $entries[0];
			$accountId = $account->id;

			try {

				$bearerAuth = 'Bearer  ' . $accessToken;

				$headers = array(
					'Authorization' => $bearerAuth,
					'Accept' 	=> 'application/json',
					'User-Agent' 	=> 'XPD-AWeber-get-account',
				);

				$body = [];

				$payLoad = array(
					'method' => 'GET',
					'headers' => $headers,
					'body' => $body
				);

				$listUrl = $this->build_list_fetching_url($accountId);

				$response1 = wp_remote_post($listUrl, $payLoad);

			} catch(\Exception $ex) {

				return wp_send_json_error([
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Account retrieve success but could not retrieved the list due to api call fail! - ['. $ex->getMessage().']',
				]);
			}


			$json = json_decode($response1['body']);

			if(!empty($json)) {

				if(property_exists($json, 'error')) {

					return wp_send_json_error([
						'result' => XPD_Constants::RETURN_NOT_OKAY,
						'msg' => 'Error returned while getting lists. ['.$json->error->message.']',
						'msg3' => $json->error,
					]);
				}

				$acOptions = [];

				if(!empty($json->entries)) {

					foreach($json->entries as $entry) {

						$tmp = [];
						$tmp['id'] = $entry->id;
						$tmp['name'] = $entry->name;
						$tmp['s_link'] = $entry->subscribers_collection_link;

						$acOptions[$entry->id] = $tmp;
					}

					update_option(self::AWEBER_LISTS_CACHE_KEY, $acOptions);
				}

				return wp_send_json_success([
					'result' => XPD_Constants::RETURN_OKAY,
					'lists' => $acOptions,
					'msg' => 'successfully retrieved.',
				]);
			}


			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Something very awful happened! Southern army attacked the Northern army :(, please try again later.'
			]);
		}


		return wp_send_json_error([
			'result' => XPD_Constants::RETURN_NOT_OKAY,
			'msg' => 'Could not retrieved account information, empty body returned from aweber server.'
		]);
	}

	/**
	 * Check if the aweber is authorized for this app
	 *
	 * @return bool
	 */
	public function is_authorized() {

		$exist = get_option(self::AUTHORIZATION_CODE_KEY);

		return !empty($exist);
	}


	/**
	 *
	 * @return array
	 */
	public function refresh_access_token_using_refresh_token() {

		try {
			$accTkn = get_option(self::ACCESS_TOKEN_KEY);
			$authQd = get_option(self::AUTHORIZATION_CODE_KEY);
			$auth64 = get_option(self::BASIC_AUTH_64_CRED_KEY);
			$basicAuth = 'Basic ' . $auth64;

			$headers = array(
				'Authorization' => $basicAuth,
				'Content-Type' => 'application/json; charset=utf-8',
				#'Content-Type' 	=> 'application/x-www-form-urlencoded'
			);

			$body = [
				'grant_type' => 'refresh_token',
				'code' => $authQd,
				'refresh_token' => $accTkn['refresh_token'],
			];

			$payLoad = array(
				'method' => 'POST',
				'headers' => $headers,
				'body' => wp_json_encode($body)
			);

			$url = $this->access_token_url;

			$response = wp_remote_post($url, $payLoad);

		} catch(\Exception $ex) {

			return [
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Could not retrieved access token - ['. $ex->getMessage().']',
			];
		}

		if (is_wp_error($response)) {

			return [
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Could not retrieved - '. $response->get_error_message(),
			];
		}

		$json = json_decode($response['body']);

		if(!empty($json)) {

			if(property_exists($json, 'error')) {

				return [
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Error returned while getting access token using refresh token. ['.$json->error.' :: '.$json->error_description.']',
					'rf_tkn' => $accTkn['refresh_token'],
				];
			}

			$accessToken = [];
			$accessToken['retrieved']   = time();
			$accessToken['token_type']  = $json->token_type;
			$accessToken['expires_in']  = $json->expires_in;
			$accessToken['refresh_token']   = $json->refresh_token;
			$accessToken['access_token']    = $json->access_token;

			update_option(self::ACCESS_TOKEN_KEY, $accessToken);


			return [
				'result' => XPD_Constants::RETURN_OKAY,
				'token' => $accessToken['access_token'],
			];
		}


		return [
			'result' => XPD_Constants::RETURN_NOT_OKAY,
			'msg' => 'No error returned from aweber but empty body! ',
		];

	}


	/**
	 *
	 * @param $authCode
	 *
	 * @return array
	 */
	public function get_access_token_aweber_using_code($authCode) {

		$accessToken = get_option(self::ACCESS_TOKEN_KEY);

		if(empty($accessToken)) {

			#never acquired.. or reset the database...
			#get access token and refresh token using authCode

			try {
				$auth64 = get_option(self::BASIC_AUTH_64_CRED_KEY);
				$basicAuth = 'Basic ' . $auth64;

				$headers = array(
					'Authorization' => $basicAuth,
					'Content-Type' => 'application/json; charset=utf-8',
					#'Content-Type' 	=> 'application/x-www-form-urlencoded',
				);

				$body = [
					'grant_type' => 'authorization_code',
					'code' => $authCode,
					'redirect_uri' => $this->tmp_uri,
				];

				$payLoad = array(
					'method' => 'POST',
					'headers' => $headers,
					'body' => wp_json_encode($body)
				);

				$url = $this->access_token_url;

				$response = wp_remote_post($url, $payLoad);

			} catch(\Exception $ex) {

				return [
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Could not retrieved access token - ['. $ex->getMessage().']',
				];
			}

			if (is_wp_error($response)) {

				return [
					'result' => XPD_Constants::RETURN_NOT_OKAY,
					'msg' => 'Could not retrieved - '. $response->get_error_message(),
				];
			}

			$json = json_decode($response['body']);


			if(!empty($json)) {

				if(property_exists($json, 'error')) {

					return [
						'result' => XPD_Constants::RETURN_NOT_OKAY,
						'msg' => 'Error returned while getting access token using auth code. ['.$json->error.' :: '.$json->error_description.']',
					];
				}

				$accessToken = [];
				$accessToken['retrieved']   = time();
				$accessToken['token_type']  = $json->token_type;
				$accessToken['expires_in']  = $json->expires_in;
				$accessToken['refresh_token']   = $json->refresh_token;
				$accessToken['access_token']    = $json->access_token;


				update_option(self::ACCESS_TOKEN_KEY, $accessToken);

				return [
					'result' => XPD_Constants::RETURN_OKAY,
					'token' => $accessToken['access_token'],
				];
			}


			return [
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'No error returned from aweber while getting access token using auth code but empty body! ',
			];
		}


		#check the time if it is still valid
		#if not then refresh it with access token.
		$now = time();
		$ret = $accessToken['retrieved'];
		$exp = $accessToken['expires_in'];

		$refreshTheToken = ($now - $ret) > $exp ? true :false;

		if($refreshTheToken === true) {

			#get refresh token and update it......

			return $this->refresh_access_token_using_refresh_token();
		}

		return [
			'result' => XPD_Constants::RETURN_OKAY,
			'token' => $accessToken['access_token'],
		];
	}


	/**
	 *
	 * @return array
	 */
	public function get_access_token() {

		$authQd = get_option(self::AUTHORIZATION_CODE_KEY);

		if(empty($authQd)) {
			#get an authorization code first.......
			## then get access token and refresh token

			return [
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'action_need' => 'get_auth_code',
				'msg' => 'No auth code found!',
			];
		}

		return $this->get_access_token_aweber_using_code($authQd);
	}


	/**
	 *
	 * @param array $config
	 *
	 * @return string
	 */
	protected function build_authorization_url(array $config = []) {

		$config['response_type'] = empty($config['response_type']) ? 'code' : $config['response_type'];

		return $this->authorization_url . '?' . http_build_query($config);
	}


	/**
	 * Get the authorization url
	 *
	 * @return mixed
	 */
	public function get_aweber_authorization_url() {

		if($this->is_authorized()) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'Already authorized with these key - ['.$_REQUEST['api_key'].':'.$_REQUEST['api_sec'].']',
			]);
		}

		$url = $this->prepare_authorization_url();

		return wp_send_json_success([
			'result' => XPD_Constants::RETURN_OKAY,
			'msg' => 'Not authorized.',
			'url' => $url,
		]);
	}


	/**
	 *
	 * @return mixed
	 */
	public function get_aweber_re_authorization_url() {

		if(empty($_REQUEST['api_key'])) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_NOT_OKAY,
				'msg' => 'validation error : api key must be provided.',
			]);
		}

		$isPKc = empty($_REQUEST['api_sec']);

		$url = $this->prepare_authorization_url();

		return wp_send_json_success([
			'result' => XPD_Constants::RETURN_OKAY,
			'is_pkse' => $isPKc,
			'url' => $url,
		]);
	}


	/**
	 *
	 * @return string
	 */
	function prepare_authorization_url() {

		$config = [];

		$config['client_id'] = $_REQUEST['api_key'];

		$basicAuth = $_REQUEST['api_key'].':';

		if(!empty($_REQUEST['api_sec'])) {
			$config['client_secret'] = $_REQUEST['api_sec'];
			$basicAuth .= $config['client_secret'];
		}

		$config['state'] = wp_create_nonce( 'met_form_12345_nonce' );

		update_option(self::NONCE_VERIFICATION_KEY, $config['state']);
		update_option(self::BASIC_AUTH_64_CRED_KEY, base64_encode($basicAuth));

		$scopes = array(
			'account.read',
			'list.read',
			'list.write',
			'subscriber.read',
			'subscriber.write',
			'email.read',
			'email.write',
			'subscriber.read-extended'
		);

		$config['redirect_uri'] = $this->tmp_uri;
		$config['scope'] = implode(' ', $scopes);

		return $this->build_authorization_url($config);
	}



	/**
	 *
	 * @return mixed
	 */
	public function get_authorization_code() {

		try {

			$config = [];

			$config['client_id'] = $_REQUEST['api_key'];
			$config['client_secret'] = $_REQUEST['api_sec'];
			$config['response_type'] = 'code';


			$config['redirect_uri'] = $this->tmp_uri;
			$config['scope'] = 'intended_url';
			$config['state'] = 'a_wp_nonce_for_csrf_by_us';


		} catch(\Exception $ex) {

			return wp_send_json_error([
				'result' => XPD_Constants::RETURN_OKAY,
				'is_authorized' => 'no',
				'msg' => 'Authorization code could not be retrieved - ['. $ex->getMessage().']',
			]);
		}

		return wp_send_json_error([
			'result' => XPD_Constants::RETURN_OKAY,
			'is_authorized' => 'no',
			'msg' => 'Not authorized.',
			'msg1' => $_REQUEST['api_key'],
			'msg3' => $_REQUEST['api_sec'],
		]);
	}


	/**
	 *
	 * @param $form_data
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function call_api($form_data, $settings) {

		$listId     = $settings['mail_settings']['mf_aweber_list_id'];
		$emailFld   = $settings['email_name'];
		$fNm        = (isset($form_data['mf-listing-fname']) ? $form_data['mf-listing-fname'] : 'NF') ;
		$email      = (isset($form_data[$emailFld]) ? $form_data[$emailFld] : '') ;

		$data['email'] = $email;
		$data['name'] = $fNm;

		return $this->add_subscriber_to_form($listId, $data);
	}


	/**
	 *
	 * @param $formId
	 * @param $form_data
	 *
	 * @return array
	 */
	public function add_subscriber_to_form($formId, $form_data) {

		$cacheList = get_option(self::AWEBER_LISTS_CACHE_KEY);
		$return = [];

		if(empty($cacheList[$formId])) {

			#error .........

			$return['status'] = 0;
			$return['msg'] = esc_html__('Lists could not found in cache!, please refresh the lists first.', 'metform-pro');

			return $return;
		}

		$accessToken = $this->get_access_token();

		if(empty($accessToken['token'])) {

			$return['status'] = 0;
			$return['msg'] = esc_html__('Failed to retrieve access token for aweber! action could not be performed.', 'metform-pro');

			return $return;
		}

		$config = [];
		$config['email']    = $form_data['email'];
		$config['name']     = $form_data['name'];
		#$config['misc_notes']     = '';
		#$config['ad_tracking']    = '';
		#$config['custom_fields']  = ['key1' => 'val1', 'key2' => 'val2',];
		#$config['tags']           = [112, 114];


		try {

			$bearerAuth = 'Bearer  ' . $accessToken['token'];

			$headers = array(
				'Authorization' => $bearerAuth,
				'Content-Type' => 'application/json; charset=utf-8',
				'Accept' 	=> 'application/json',
				'User-Agent' 	=> 'XPD-AWeber-get-account',
			);

			$payLoad = array(
				'headers' => $headers,
				'method'  => 'POST',
				'body'    => wp_json_encode($config),
			);

			$url = $cacheList[$formId]['s_link'];

			$response = wp_remote_post($url, $payLoad);

		} catch(\Exception $ex) {

			$return['status'] = 0;
			$return['msg'] = "Something went wrong: " . esc_html($ex->getMessage());

			return $return;
		}

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			$return['status'] = 0;
			$return['msg'] = "Something went wrong: " . esc_html($error_message);

			return $return;
		}

		$return['status'] = 1;
		$return['msg'] = esc_html__('Your data inserted on ConvertKit.', 'metform-pro');

		return $return;
	}

}