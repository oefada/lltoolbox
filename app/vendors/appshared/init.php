<?php

class AppShared {
	function __construct($pathsOnly = true) {
		if(
			!isset($_SERVER['DOCUMENT_ROOT'])
			|| ($_SERVER['DOCUMENT_ROOT'] == '')
			|| ($_SERVER['DOCUMENT_ROOT'] == '/var/www/luxurylink/php')
		){
			$frameworkPath='/var/www/appshared/framework/';
		} else {
			$frameworkPath='/home/' . $_SERVER['ENV_USER'] . '/appshared/framework/';
		}

		
		Configure::write("Appshared.Path",$frameworkPath);
		
		if (!$pathsOnly) {
			require_once($frameworkPath.'Launchpad.php');
			Launchpad::init('luxurylink', 'ConfigLL');
		}
	}
}

?>