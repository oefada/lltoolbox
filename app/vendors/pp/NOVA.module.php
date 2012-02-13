<?php

// NOVA (MYVIRTUAL MERCHANT) PAYMENT MODULE
// --------------------------------------------------------------------------
// To be used with Processor.class.php only.  [alee@luxurylink.com]
// Note:  See bottom of this page for sample responses

class NOVA
{
	var $url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
	var $map_params;
	var $post_data;
	private $valid_avs_codes = array("F","D","M","P","W","X","Y","Z");
	
	function NOVA($test_param = FALSE) {
		$this->post_data = array();
		$this->post_data['ssl_merchant_id'] 		= '506345';
		$this->post_data['ssl_user_id'] 			= 'web';
		$this->post_data['ssl_pin'] 				= '252176';
		$this->post_data['ssl_pin'] 				= '252176';
		$this->post_data['ssl_transaction_type']	= 'ccavsonly';
		$this->post_data['ssl_test_mode'] 			= $test_param ? 'TRUE' : 'FALSE';
		$this->post_data['ssl_result_format'] 		= 'ASCII';
		$this->post_data['ssl_show_form']	 		= '0';
		$this->post_data['ssl_cvv2cvc2_indicator'] 	= '0';
		$this->post_data['ssl_salestax'] 			= '0';

		$this->map_params = array();   
		$this->map_params['map_ticket_id'] 			= 'ssl_invoice_number'; // 25     
		$this->map_params['map_total_amount'] 		= 'ssl_amount'; 	   // 13	 
		$this->map_params['map_first_name'] 		= 'ssl_first_name';    // 20     25
		$this->map_params['map_last_name'] 			= 'ssl_last_name';      // 30     25
		$this->map_params['map_street'] 			= 'ssl_avs_address';       // 20     60
		$this->map_params['map_street2'] 			= 'ssl_address2';         // 30     xx
		$this->map_params['map_city'] 				= 'ssl_city';                // 30     30    
		$this->map_params['map_state'] 				= 'ssl_state';              // 30     2
		$this->map_params['map_zip'] 				= 'ssl_avs_zip';              // 9      10
		$this->map_params['map_country'] 			= 'ssl_country';          // 50
		$this->map_params['map_expiration'] 		= 'ssl_exp_date';      // 4      2
		$this->map_params['map_card_num'] 			= 'ssl_card_number';     // 19    
	}

	function ProcessResponse($raw_response) {
		$processed = array();
		$tmp_array = explode("\n", strval($raw_response));
		foreach($tmp_array as $k=>$v) {
			$tmp = explode('=', $v);
			$processed[$tmp[0]] = (isset($tmp[1])) ? $tmp[1] : 0;
		}

		$processed['avs_only'] = false;
				
		if ($this->post_data['ssl_transaction_type'] == 'ccavsonly') {
			$processed['avs_only'] = true;
		}

		return $processed;
	}

	public function ChargeSuccess($response) {
		if(isset($response['ssl_result'])) {
			if($response['ssl_result'] == 0 && in_array($response['ssl_avs_response'],$this->valid_avs_codes)) {
				return true;
			}
		}
		
		return false;
	}

	function GetMappedResponse($response) {
		$paymentDetail = array();
		
		$paymentDetail['ppResponseDate']		= date('Y-m-d H:i:s', strtotime('now'));
		$paymentDetail['ppTransactionId']		= (isset($response['ssl_txn_id'])) ? $response['ssl_txn_id'] : 0;
		$paymentDetail['ppApprovalText']		= (isset($response['ssl_result_message'])) ? $response['ssl_result_message'] : 0;
		$paymentDetail['ppApprovalCode']		= (isset($response['ssl_result'])) ? $response['ssl_result'] : 0;
		$paymentDetail['ppAvsCode']				= (isset($response['ssl_avs_response'])) ? $response['ssl_avs_response'] : 0;
		$paymentDetail['ppResponseText']		= '';
		$paymentDetail['ppResponseSubCode']		= '';
		$paymentDetail['ppReasonCode']			= '';
		$paymentDetail['isSuccessfulCharge']	= $this->ChargeSuccess($response) ? '1' : '0';
		
		return $paymentDetail;
	}
	
	function IsValidResponse($response, $valid_param) {
		if(isset($response['ssl_invoice_number'])) {
			if(trim($response['ssl_invoice_number']) == trim($valid_param)) {
				return true;
			}else return false;
		}else return false;
	}

	function GetResponseTxt($response) {
		if (isset($response['ssl_avs_response']) && !in_array($response['ssl_avs_response'],$this->valid_avs_codes)) {
			return "NO_AVS";
		} elseif(isset($response['ssl_result_message'])) {
			return $response['ssl_result_message'];
		}else {
			return false;
		}
	}

	public function getPostSale()
	{
		$this->post_data['ssl_transaction_type'] = 'ccsale';
		return array('ssl_transaction_type' => 'ccsale');
	}
}

/* 
----------------------------------------------------------------
SAMPLE RESPONSES -- ALL RESPONSES ARE DATATYPE (STRING)
----------------------------------------------------------------

[INVALID CARD]

ssl_card_number=41********1111
ssl_exp_date=0611
ssl_amount=1.00
ssl_customer_code=
ssl_salestax=0.00
ssl_invoice_number=132433
ssl_surcharge_amount=
ssl_reference_number=
ssl_original_date=
ssl_original_time=
ssl_tran_code=
ssl_sku_number=
ssl_egc_tender_type=
ssl_account_type=*
ssl_customer_number=
ssl_result=1
ssl_result_message=INVALID CARD
ssl_txn_id=156A4BE6F-4586-E942-14BD-143208212832
ssl_approval_code=      
ssl_cvv2_response=
ssl_avs_response=U
ssl_account_balance=0.00
ssl_txn_time=02/16/2009 04:48:41 PM


[DECLINED]

ssl_card_number=46********0365
ssl_exp_date=1210
ssl_amount=1.00
ssl_customer_code=
ssl_salestax=0.00
ssl_invoice_number=132433
ssl_surcharge_amount=
ssl_reference_number=
ssl_original_date=
ssl_original_time=
ssl_tran_code=
ssl_sku_number=
ssl_egc_tender_type=
ssl_account_type=*
ssl_customer_number=
ssl_result=1
ssl_result_message=DECLINED
ssl_txn_id=29B77966C-506D-9689-9EF8-7AB147D3D90A
ssl_approval_code=      
ssl_cvv2_response=
ssl_avs_response=N
ssl_account_balance=0.00
ssl_txn_time=02/16/2009 05:02:09 PM

[INVALID EXP]

ssl_card_number=46********0365
ssl_exp_date=0009
ssl_amount=1.00
ssl_customer_code=
ssl_salestax=0.00
ssl_invoice_number=132433
ssl_surcharge_amount=
ssl_reference_number=
ssl_original_date=
ssl_original_time=
ssl_tran_code=
ssl_sku_number=
ssl_egc_tender_type=
ssl_account_type=*
ssl_customer_number=
ssl_result=1
ssl_result_message=INV EXP DATE
ssl_txn_id=2CC5D1DFF-6911-A6F3-C842-5D42A35E1A19
ssl_approval_code=      
ssl_cvv2_response=
ssl_avs_response= 
ssl_account_balance=0.00
ssl_txn_time=02/16/2009 05:03:53 PM

[APPROVAL]

ssl_card_number=46********0365
ssl_exp_date=0909
ssl_amount=1.00
ssl_customer_code=
ssl_salestax=0.00
ssl_invoice_number=132433
ssl_surcharge_amount=
ssl_reference_number=
ssl_original_date=
ssl_original_time=
ssl_tran_code=
ssl_sku_number=
ssl_egc_tender_type=
ssl_account_type=*
ssl_customer_number=
ssl_result=0
ssl_result_message=APPROVAL
ssl_txn_id=122049FAC-200A-D9F0-DE8D-3BA196FCE1DC
ssl_approval_code=338962
ssl_cvv2_response=
ssl_avs_response=N
ssl_account_balance=0.00
ssl_txn_time=02/16/2009 05:05:17 PM

----------------------------------------------------------------
END OF SAMPLE RESPONSES -- ALL RESPONSES ARE DATATYPE (STRING)
----------------------------------------------------------------
*/