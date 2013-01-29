<?php

header('Content-type: application/json');

$data = array();

if (isset($_REQUEST['block'])) {

	$basepath = realpath(APP . 'webroot' . DS . 'images' . DS . 'blocks');
	$baseurl = 'http://img.llsrv.us/images/blocks' . $_REQUEST['block'];
	$blockpath = realpath($basepath . $_REQUEST['block']);
	if (substr($blockpath, 0, strlen($basepath)) === $basepath) {
		foreach (glob($blockpath.DS.'{*.jpg,*.png}',GLOB_BRACE) as $file) {
			$data[] = $baseurl . DS . basename($file);
		}
	}

}

echo json_encode($data);
