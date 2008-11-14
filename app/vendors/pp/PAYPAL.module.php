<?php

class PAYPAL
{
	var $url = 'https://payflowpro.paypal.com';
	var $map_params;
	var $post_data;

	function PAYPAL($test_param = FALSE) {
		if ($test_param) {
			$this->url = 'https://pilot-payflowpro.paypal.com';
		}
		
		$this->post_data = array();
		$this->post_data['USER']       = 'LuxuryLink5200';
		$this->post_data['VENDOR']     = 'LuxuryLink5200';
		$this->post_data['PARTNER']    = 'PayPal';
		$this->post_data['PWD']        = 'luxurylink00';
		$this->post_data['TENDER']     = 'C';  // C - Direct Payment using credit card
		$this->post_data['TRXTYPE']    = 'S';  // A - Authorization, S - Sale
		$this->post_data['CUSTIP']     = $_SERVER['REMOTE_ADDR'];
		$this->post_data['VERBOSITY']  = 'LOW';
		$this->post_data['CURRENCY']   = 'USD';

		$this->map_params = array();                               
		$this->map_params['map_ticket_id'] 		= 'COMMENT1';      
		$this->map_params['map_total_amount'] 	= 'AMT'; 	   	 
		$this->map_params['map_description'] 	= 'COMMENT2';  
		$this->map_params['map_first_name'] 	= 'FIRSTNAME';    
		$this->map_params['map_last_name'] 		= 'LASTNAME';    
		$this->map_params['map_street'] 		= 'STREET';       
		$this->map_params['map_city'] 			= 'CITY';              
		$this->map_params['map_state'] 			= 'STATE';              
		$this->map_params['map_zip'] 			= 'ZIP';             
		$this->map_params['map_expiration'] 	= 'EXPDATE';  
		$this->map_params['map_card_num'] 		= 'ACCT';     
		$this->map_params['map_country'] 		= 'COUNTRY'; 
	}

	function ProcessResponse($raw_response) {
		// sample response:
		// RESULT=12&PNREF=ESHP2C4F2F31&RESPMSG=Declined: 15005-This transaction cannot be processed.&AVSADDR=N&AVSZIP=N&CVV2MATCH=X&IAVS=N   DECLINE
		// RESULT=0&PNREF=EPFP2CF2D63B&RESPMSG=Approved&AUTHCODE=111111&AVSADDR=N&AVSZIP=N&CVV2MATCH=X&PPREF=02U25249S18411518&CORRELATIONID=74f8f5043843b&IAVS=N SUCCESS

		$processed = array();
		$tmp_array = split('&', strval($raw_response));
		foreach($tmp_array as $k=>$v) {
			$tmp = explode('=', $v);
			$processed[$tmp[0]] = $tmp[1];
		}
		return $processed;
	}

	function ChargeSuccess($response) {
		if(isset($response['RESULT'])) {
			if($response['RESULT'] == 0) {
				return true;
			}else return false;
		}else return false;
	}

	/*
	function UpdateCCTxt($txt_submission_id, $response, $initials = '') {
		global $C_connection;
		$cc_ini = $initials ? $initials : 'AUTO';
		$query = "UPDATE cc_txn_mstr SET "
			. "txtTransactionID = '$response[PNREF]',"
			. "txtCardService = 'PAYP',"
			. "txtResponseDate = getDate(),"
			. "txtApprovalStatus = '$response[RESULT]',"
			. "txtApprovalCode = '$response[AUTHCODE]',"
			. "txtAVSCode = '$response[PPREF]',"
			. "txtAVSResponseText = '$response[RESPMSG]',"
			. "txtResponseSubcode = '$response[CORRELATIONID]',"
			. "txtInitials = '$cc_ini' "
			. "WHERE txtSubmissionID = '$txt_submission_id'";

		$update_cc_txt = mssql_query($query,$C_connection);
	}
	*/
	
	function IsValidResponse($response, $valid_param) {
		if (isset($response['PNREF']) && isset($response['RESULT'])) {
			return true;
		} else {
			return false;		
		}
	}

	function GetResponseTxt($response) {
		if(isset($response['RESPMSG'])) {
			return $response['RESPMSG'];
		}else {
			return false;
		}
	}
}
?>
