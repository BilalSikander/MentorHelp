<?php

namespace MetForm_Pro\Core\Integrations\Email\Activecampaign;

use MetForm_Pro\Base\Api;

class Active_Campaign_Route extends Api {

	const SK_API_KEY = 'mf_active_campaign_api_key';
	const SK_CAMP_URL = 'mf_active_campaign_url';
	const METFORM_SETTINGS_KEY_ALL = 'metform_option__settings';

	public function config() {

		$this->prefix = 'active-campaign';

		/**
		 * Be careful regarding this value
		 */
		$this->param = "";
	}


	public function get_email_lists() {

		$sett = $this->retrieve_api_key();

		if(empty($sett[self::SK_CAMP_URL]) || empty($sett[self::SK_API_KEY])) {

			return wp_send_json_error(
				[
					'msg' => esc_html__('Campaign url or API key is not yet set!', 'metform-pro'),
				]
			);
		}

		$token = $sett[self::SK_API_KEY];
		$uri = $sett[self::SK_CAMP_URL];
		$full_url = $uri . '/api/3/lists';


		$config = [];

		$headers = [
			'Content-Type' => 'application/json; charset=utf-8',
			'Api-Token'    => $token,
		];

		$payLoad = [
			'headers' => $headers,
			'method'  => 'GET',
			'body'    => $config,
		];


		try {

			$response = wp_remote_get($full_url, $payLoad);

		} catch(\Exception $ex) {

			return wp_send_json_error(
				[
					'msg' => $ex->getMessage(),
				]
			);
		}

		if(is_wp_error($response)) {

			return wp_send_json_error(
				[
					'msg' => $response->get_error_message(),
				]
			);
		}

		$json = json_decode($response['body']);
		$list = [];

		if(isset($json->lists)) {

			$frm = $json->lists;

			foreach($frm as $item) {

				$tmp = [];
				$tmp['sid'] = $item->id;
				$tmp['strid'] = $item->stringid;
				$tmp['usr'] = $item->userid;
				$tmp['name'] = $item->name;

				$list[] = $tmp;
			}

			update_option(Active_Campaign::CK_ACT_CAMP_EMAIL_LIST_CACHE_KEY, $list);
		}


		return wp_send_json_success(
			[
				'result' => 'ok',
				'list'   => $list,
				'list2'  => $json,
				'msg'    => 'successfully retrieved.',
			]
		);
	}


	public function get_tag_lists() {

		$sett = $this->retrieve_api_key();

		if(empty($sett[self::SK_CAMP_URL]) || empty($sett[self::SK_API_KEY])) {

			return wp_send_json_error(
				[
					'msg' => esc_html__('Campaign url or API key is not yet set!', 'metform-pro'),
				]
			);
		}

		$token = $sett[self::SK_API_KEY];
		$uri = $sett[self::SK_CAMP_URL];
		$full_url = $uri . '/api/3/tags';


		$config = [];

		$headers = [
			'Content-Type' => 'application/json; charset=utf-8',
			'Api-Token'    => $token,
		];

		$payLoad = [
			'headers' => $headers,
			'method'  => 'GET',
			'body'    => $config,
		];


		try {

			$response = wp_remote_get($full_url, $payLoad);

		} catch(\Exception $ex) {

			return wp_send_json_error(
				[
					'msg' => $ex->getMessage(),
				]
			);
		}

		if(is_wp_error($response)) {

			return wp_send_json_error(
				[
					'msg' => $response->get_error_message(),
				]
			);
		}

		$json = json_decode($response['body']);
		$list = [];

		if(isset($json->tags)) {

			$frm = $json->tags;

			foreach($frm as $item) {

				$tmp = [];
				$tmp['sid'] = $item->id;
				$tmp['desc'] = $item->description;
				$tmp['name'] = $item->tag;

				$list[] = $tmp;
			}

			update_option(Active_Campaign::CK_ACT_CAMP_TAG_LIST_CACHE_KEY, $list);
		}


		return wp_send_json_success(
			[
				'result' => 'ok',
				'list'   => $list,
				'list2'  => $json,
				'msg'    => 'successfully retrieved.',
			]
		);
	}


	public function retrieve_api_key() {

		$sett = get_option(self::METFORM_SETTINGS_KEY_ALL);

		return $sett;
	}

	public function get_testing() {

		echo 'sdfsd fs d fdsfsd dsfs';

		return [
			'status'  => 'success',
			'list'  => [],
			'message' => esc_html__('Tags successfully fetched.', 'metform-pro'),
		];
	}

	public function post_testing() {

		return [
			'status'  => 'success',
			'list'  => [],
			'message' => esc_html__('Tags successfully fetched.', 'metform-pro'),
		];
	}
}
