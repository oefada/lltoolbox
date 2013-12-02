<?

class DebugData extends AppModel {

	var $name = 'DebugData';
	var $useTable = 'debugData';
	var $primaryKey = 'id';


	function saveDebugData($label,$more_info=''){


		$data_arr['DebugData']=array(
			'label'=>$label,
			'unixtime'=>time(),
			'post'=>serialize($_POST),
			'get'=>serialize($_GET),
			'http_referer'=>$_SERVER['HTTP_REFERER'],
			'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'server_name'=>$_SERVER['SERVER_NAME'],
			'remote_addr'=>$_SERVER['REMOTE_ADDR'],
			'cookie'=>serialize($_COOKIE),
			'more_info'=>serialize($more_info),
			'env'=>$_SERVER['ENV'],
			'request_uri'=>$_SERVER['REQUEST_URI']
		);

		if (!$this->save($data_arr)){
			$this->log("failed to save $data_arr");
		}

	}


}
