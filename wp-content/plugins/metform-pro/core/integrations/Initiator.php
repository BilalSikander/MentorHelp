<?php

namespace MetForm_Pro\Core\Integrations;


use MetForm_Pro\Core\Integrations\Email\Activecampaign\Active_Campaign_Route;

class Initiator {


	public static function autoload() {

		require __DIR__ .'/Mail_Adapter_Contract.php';
		require __DIR__ .'/Mail_Adapter.php';
		require __DIR__ .'/Aweber.php';
		require __DIR__ .'/Convert_Kit.php';
		require __DIR__ .'/Mail_Poet.php';
	}

	public static function initiate() {

		$aweber = new Aweber();
		$cKit   = new Convert_Kit();
		$mPoet  = new Mail_Poet();

		#routes
		new Active_Campaign_Route();
	}
}
