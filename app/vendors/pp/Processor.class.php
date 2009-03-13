<?php

require(APP.'/vendors/pp/AIM.module.php');
require(APP.'/vendors/pp/PAYPAL.module.php');
require(APP.'/vendors/pp/NOVA.module.php');

class Processor
{	
	var $module;
	var $module_list = array('AIM','NOVA','PAYPAL');
	var $processor_name;
	private $response_data = array();
	private $post_data = array();
	
	function Processor($processor_name) {
		if (!in_array($processor_name, $this->module_list)) {
			return false;
		}
		$this->module = new $processor_name();
		$this->processor_name = $processor_name;
	} 
	
	function InitPayment($userPaymentSetting, $ticket) {
		// build needed parameters for a post.
		
		$ups = $userPaymentSetting['UserPaymentSetting'];
		$ups['expMonth'] = str_pad(substr($ups['expMonth'], -2, 2), 2, '0', STR_PAD_LEFT);
		$ups['expYear'] = str_pad(substr($ups['expYear'], -2, 2), 2, '0', STR_PAD_LEFT);
		$nameSplit = str_word_count($ups['nameOnCard'], 1);
		$firstName = trim($nameSplit[0]);
		$lastName = trim(array_pop($nameSplit));
		
		$db_params = array();
		$db_params['map_ticket_id'] 				= $ticket['Ticket']['ticketId'];
		$db_params['map_total_amount'] 				= $ticket['Ticket']['billingPrice'];
		$db_params['map_first_name'] 				= substr($firstName, 0, 20);
		$db_params['map_last_name'] 				= substr($lastName, 0, 20);
		$db_params['map_street'] 					= substr(trim($ups['address1']), 0, 20);
		$db_params['map_street2'] 					= substr(trim($ups['address2']), 0, 20);
		$db_params['map_city'] 						= substr(trim($ups['city']), 0, 20);
		$db_params['map_state'] 					= substr(trim($ups['state']), 0, 20);
		$db_params['map_zip'] 						= substr(trim($ups['postalCode']),0, 9);
		$db_params['map_country'] 					= substr(trim($ups['country']),0,20);
		$db_params['map_expiration'] 				= $ups['expMonth'] . $ups['expYear']; 
		$db_params['map_card_num'] 					= trim($ups['ccNumber']);
		
		$this->post_data = $this->MapParams($db_params);
	}

	function SubmitPost() {
		if (!is_array($this->post_data) || !$this->post_data || empty($this->post_data)) {
			return false;
		}

		$post_string = $this->SetPostFields($this->post_data);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->module->url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

		$response = curl_exec($ch);
		curl_close($ch);		
		
		$this->response_data = $this->module->ProcessResponse($response);
		
		$this->post_data = array();
		unset($post_string);
		unset($response);
		unset($ch);
	}

	function ChargeSuccess() {
		return $this->module->ChargeSuccess($this->response_data);
	}

	function GetResponseTxt() {
		return $this->module->GetResponseTxt($this->response_data);
	}
	
	function GetMappedResponse() {
		return $this->module->GetMappedResponse($this->response_data);
	}

	function IsValidResponse($ticket_id) {
		return $this->module->IsValidResponse($this->response_data, $ticket_id);
	}

	private function MapParams($params) {
		if (!is_array($params)) {
			return false;
		}
		$tmp = array();
		foreach ($this->module->map_params as $k => $v) {
			if (isset($params[$k])) {
				$tmp[$v] = $params[$k];
			}
		}
		return array_merge($this->module->post_data, $tmp);
	}

	private function SetPostFields($params) {
	    $tmp_str = '';
	    foreach ($params as $k => $v) {
	 	   $tmp_str .= "$k=" . urlencode($v) . '&';
		}
		return $tmp_str;
	}
}
?>
