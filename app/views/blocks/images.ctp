<?php

header('Content-type: application/json');

$data = array();

if (isset($_REQUEST['block'])) {

	$basepath = realpath(APP . 'webroot' . DS . 'images' . DS . 'blocks');
	$baseurl = '/images/blocks' . $_REQUEST['block'];
	$blockpath = realpath($basepath . $_REQUEST['block']);
	if (substr($blockpath, 0, strlen($basepath)) === $basepath) {
		foreach (glob($blockpath.DS.'{*.jpg,*.png}',GLOB_BRACE) as $file) {
			$data[] = $baseurl . DS . basename($file);
		}
	} else {
		try {
			$newPath = '\blocks' . str_replace('/', '\\', strtolower($_REQUEST['block']));
			$newPath = str_replace('.', '', $newPath);
			$newPath = preg_replace('/\\+/', '\\', $newPath);
			$newPath = preg_replace('/[^a-z0-9\-\_\\\\]/', '', $newPath);
			$cmd = 'smbclient -U images -c "cd \\';
			$xPath = '';
			foreach (explode('\\',$newPath) as $np) {
				if ($np) {
					$xPath .= ($xPath ? '\\' : '') . $np;
					$cmd .= '; mkdir ' . $xPath;
				}
			}
			$cmd .= '" //images.luxurylink.com/images ' . base64_decode('aTJMVmxs') . ' &';
			shell_exec($cmd);
		} catch (Exception $e) {
		}
	}

}

echo json_encode($data);
