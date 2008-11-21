<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceBidsController extends WebServicesController
{
	var $name = 'WebServiceBids';
	var $uses = 'bid';
	var $serviceUrl = 'http://192.168.100.22/web_service_bids';
	var $errorResponse = false;
	var $api = array(
					'newBidProcessor1' => array(
						'doc' => 'Any new bids on LIVE will used this webservice to update SM',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function newBidProcessor1($in0)
	{
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->pushBidToSm($json_decoded)) {
			$json_decoded['response'] = $this->errorResponse;
		} 
		return json_encode($json_decoded);
	}
	
	function pushBidToSm($data) {
		if (!isset($data['bidId'])) {
			$this->errorResponse = 501;	
			return false;
		}
		if (empty($data['bidId'])) {
			$this->errorResponse = 502;	
			return false;
		}
		if (!is_int($data['bidId'])) {
			$this->errorResponse = 503;
			return false;	
		}
		
		$bid = new Bid();
		$bid->skipBeforeSaveFilter = true;
		$bidData  = $bid->read(null, $data['bidId']);
	
		$bidSave = array();
		$bidSave['Bid'] = $data;
	
		if (!$bidData) {
			if ($bid->save($bidSave)) {
				return true;
			} else {
				$this->errorResponse = 504;
				return false;	
			}
		} else {
			$this->errorResponse = 505;
			return false;
		}
	}

}
?>