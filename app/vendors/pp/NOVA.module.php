<?php

class NOVA
{
	var $url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
	var $map_params;
	var $post_data;

	function NOVA($test_param = FALSE) {
		$this->post_data = array();
		$this->post_data['ssl_merchant_id'] = '506345';
		$this->post_data['ssl_user_id'] = 'web';
		$this->post_data['ssl_pin'] = '252176';
		$this->post_data['ssl_transaction_type'] = 'CCSALE';
		$this->post_data['ssl_test_mode'] = $test_param ? 'TRUE' : 'FALSE';
		$this->post_data['ssl_result_format'] = 'ASCII';
		$this->post_data['ssl_show_form'] = '0';
		$this->post_data['ssl_cvv2cvc2_indicator'] = '0';
		$this->post_data['ssl_salestax'] = '0';

		$this->map_params = array();                               // MD     DB
		$this->map_params['map_ticket_id'] = 'ssl_invoice_number'; // 25     
		$this->map_params['map_total_amount'] = 'ssl_amount'; 	   // 13	 
		$this->map_params['map_description'] = 'ssl_description';  // 255    255
		$this->map_params['map_first_name'] = 'ssl_first_name';    // 20     25
		$this->map_params['map_last_name'] = 'ssl_last_name';      // 30     25
		$this->map_params['map_street'] = 'ssl_avs_address';       // 20     60
		//$this->map_Params['map_street2'] = 'ssl_address2';         // 30     xx
		$this->map_params['map_city'] = 'ssl_city';                // 30     30    
		$this->map_params['map_state'] = 'ssl_state';              // 30     2
		$this->map_params['map_zip'] = 'ssl_avs_zip';              // 9      10
		$this->map_params['map_expiration'] = 'ssl_exp_date';      // 4      2
		$this->map_params['map_card_num'] = 'ssl_card_number';     // 19    
		//$this->map_params['map_country'] = 'ssl_country';          // 50
	}

	function ProcessResponse($raw_response) {
		$processed = array();
		$tmp_array = split("\n", strval($raw_response));
		foreach($tmp_array as $k=>$v) {
			$tmp = explode('=', $v);
			$processed[$tmp[0]] = $tmp[1];
		}
		return $processed;
	}

	function ChargeSuccess($response) {
		if(isset($response['ssl_result'])) {
			if($response['ssl_result'] == 0) {
				return true;
			}else return false;
		}else return false;
	}

	/*
	function UpdateCCTxt($txt_submission_id, $response, $initials = '') {
		global $C_connection;
		$cc_ini = $initials ? $initials : 'AUTO';
		$query = "UPDATE cc_txn_mstr SET "
			. "txtTransactionID = '$response[ssl_txn_id]',"
			. "txtCardService = 'NOVA',"
			. "txtResponseDate = getdate(),"
			. "txtApprovalStatus = '$response[ssl_result]',"
			. "txtApprovalCode = '$response[ssl_approval_code]',"
			. "txtAVSCode = '$response[ssl_avs_response]',"
			. "txtAVSResponseText = '$response[ssl_result_message]',"
			. "txtResponseSubcode = '$response[ssl_cvv2_response]',"
			. "txtReasonCode = '',"
			. "txtInitials = '$cc_ini' "
			. "WHERE txtSubmissionID = '$txt_submission_id'";

		$update_cc_txt = mssql_query($query,$C_connection);
	}
	*/
	
	function IsValidResponse($response, $valid_param) {
		if(isset($response['ssl_invoice_number'])) {
			if(trim($response['ssl_invoice_number']) == trim($valid_param)) {
				return true;
			}else return false;
		}else return false;
	}

	function GetResponseTxt($response) {
		if(isset($response['ssl_result_message'])) {
			return $response['ssl_result_message'];
		}else {
			return false;
		}
	}
}
?>
