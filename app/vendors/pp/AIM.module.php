<?php

// AIM (AUTHORIZE.NET) PAYMENT MODULE
// --------------------------------------------------------------------------
// To be used with Processor.class.php only.  [alee@luxurylink.com]
// Note:  See bottom of this page for sample responses

class AIM
{
	var $url = 'https://secure.authorize.net/gateway/transact.dll';
	var $map_params;
	var $post_data;

	function AIM($test_param = FALSE) {
		
		$this->post_data = array();
		$this->post_data['x_version'] 			= '3.1';
		$this->post_data['x_delim_data']		= 'TRUE';
		$this->post_data['x_delim_char'] 		= '|';
		$this->post_data['x_encap_char'] 		= '';
		$this->post_data['x_relay_response'] 	= 'FALSE';
		$this->post_data['x_login'] 			= 'LuxuryLink5200';
		$this->post_data['x_tran_key'] 			= 'zWBPWcEWgz9HPQYP';
		$this->post_data['x_test_request'] 		= $test_param ? 'TRUE' : 'FALSE';
		$this->post_data['x_method'] 			= 'CC';
		$this->post_data['x_type'] 				= 'AUTH_CAPTURE';
		
		$this->map_params = array();
		$this->map_params['map_ticket_id'] 		= 'x_invoice_num';
		$this->map_params['map_total_amount'] 	= 'x_amount';
		$this->map_params['map_first_name'] 	= 'x_first_name';
		$this->map_params['map_last_name'] 		= 'x_last_name';
		$this->map_params['map_street'] 		= 'x_address';
		$this->map_params['map_city'] 			= 'x_city';
		$this->map_params['map_state'] 			= 'x_state';
		$this->map_params['map_zip'] 			= 'x_zip';
		$this->map_params['map_country'] 		= 'x_country';
		$this->map_params['map_expiration'] 	= 'x_exp_date';
		$this->map_params['map_card_num'] 		= 'x_card_num';
	}

	function ProcessResponse($raw_response) {
		$tmp_array = split('[|]', strval($raw_response));
		return $tmp_array;
	}

	function ChargeSuccess($response) {
		if(isset($response[0])) {
			if($response[0] == 1) {
				return true;
			}else return false;
		}else return false;
	}

	function GetMappedResponse($response) {
		$paymentDetail = array();
		
		$paymentDetail['ppResponseDate']		= date('Y-m-d H:i:s', strtotime('now'));
		$paymentDetail['ppTransactionId']		= $response[6];
		$paymentDetail['ppApprovalText']		= $response[3];
		$paymentDetail['ppApprovalCode']		= $response[0];
		$paymentDetail['ppAvsCode']				= $response[5];
		$paymentDetail['ppResponseText']		= $response[37];
		$paymentDetail['ppResponseSubCode']		= $response[4];
		$paymentDetail['ppReasonCode']			= $response[7];
		$paymentDetail['isSuccessfulCharge']	= ($response[0] == 1) && (stristr($response[3], 'APPROVED')) ? 1 : 0;
		
		return $paymentDetail;
	}
	
	function IsValidResponse($response, $valid_param) {
		if(isset($response[7])) {
			if(trim($response[7]) == trim($valid_param)) {
				return true;
			}else return false;
		}else return false;
	}

	function GetResponseTxt($response) {
		if(isset($response[3])) {
			return $response[3];
		}else {
			return false;
		}
	}
}


?>
