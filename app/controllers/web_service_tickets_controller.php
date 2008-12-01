<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';
	var $uses = array('Ticket', 'User', 'Offer', 'Bid', 'ClientLoaPackageRel', 'RevenueModelLoaRel');
	var $serviceUrl = 'http://192.168.100.111/web_service_tickets';
	var $errorResponse = false;
	var $api = array(
					'newTicketProcessor1' => array(
						'doc' => 'Receive new requests or winning bids from the ESB and create a new ticket',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'updateTrackDetail' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function newTicketProcessor1($in0)
	{
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->createNewTicket($json_decoded)) {
			$json_decoded['response'] = $this->errorResponse;
		} 
		return json_encode($json_decoded);
	}
	
	function createNewTicket($data) {
		if (empty($data) || !is_array($data)) {
			$this->errorResponse = 905;
			return false;	
		}
		if (!isset($data['userId']) || empty($data['userId'])) {
			$this->errorResponse = 906;
			return false;	
		}
		if (!isset($data['offerId']) || empty($data['offerId'])) {
			$this->errorResponse = 907;
			return false;	
		}
		
		$userData = $this->User->read(null, $data['userId']);
		
		$this->Offer->recursive = 2;
		$offerData = $this->Offer->read(null, $data['offerId']);
		$offerTypeToFormat = $this->Offer->query("SELECT formatId FROM formatOfferTypeRel WHERE offerTypeId = " . $offerData['Offer']['offerTypeId']);
		$formatId = $offerTypeToFormat[0]['formatOfferTypeRel']['formatId'];
		
		if (isset($data['bidId']) && !empty($data['bidId'])) {
			$this->Bid->recursive = -1;
			$bidData = $this->Bid->read(null, $data['bidId']);	
		}
	
		$newTicket = array();
		$newTicket['Ticket']['ticketStatusId'] 			 = 1;
		$newTicket['Ticket']['packageId'] 				 = $data['packageId'];
		$newTicket['Ticket']['offerId'] 				 = $data['offerId'];  
		$newTicket['Ticket']['offerTypeId'] 			 = $offerData['Offer']['offerTypeId'];
		$newTicket['Ticket']['formatId'] 				 = $formatId;
		
		if (isset($data['requestQueueId'])) {
			$newTicket['Ticket']['requestQueueId']     	 = $data['requestQueueId'];
			$newTicket['Ticket']['requestQueueDatetime'] = $data['requestQueueDatetime'];
			$newTicket['Ticket']['requestArrival'] 		 = $data['requestArrival'];
			$newTicket['Ticket']['requestDeparture']	 = $data['requestDeparture'];
			$newTicket['Ticket']['requestNumGuests']	 = $data['requestNumGuests'];
			$newTicket['Ticket']['requestNotes']		 = $data['requestNotes'];
			$newTicket['Ticket']['billingPrice'] 		 = $offerData['SchedulingInstance']['SchedulingMaster']['buyNowPrice'];
		} elseif (isset($data['bidId'])) {
			$newTicket['Ticket']['winningBidQueueId'] 	 = $data['winningBidQueueId'];
			$newTicket['Ticket']['bidId'] 				 = $data['bidId'];
			$newTicket['Ticket']['billingPrice'] 		 = $data['billingPrice'];
		}
		
		$newTicket['Ticket']['userId'] 					 = $userData['User']['userId'];
		$newTicket['Ticket']['userFirstName'] 			 = $userData['User']['firstName'];
		$newTicket['Ticket']['userLastName'] 			 = $userData['User']['lastName'];
		$newTicket['Ticket']['userEmail1']				 = $userData['User']['email'];
		$newTicket['Ticket']['userWorkPhone']			 = $userData['User']['workPhone'];
		$newTicket['Ticket']['userHomePhone']			 = $userData['User']['homePhone'];
		$newTicket['Ticket']['userMobilePhone']			 = $userData['User']['mobilePhone'];
		$newTicket['Ticket']['userFax'] 				 = $userData['User']['fax'];
		$newTicket['Ticket']['userAddress1']			 = $userData['Address'][0]['address1'];
		$newTicket['Ticket']['userAddress2']			 = $userData['Address'][0]['address2'];
		$newTicket['Ticket']['userAddress3']			 = $userData['Address'][0]['address3'];
		$newTicket['Ticket']['userCity']				 = $userData['Address'][0]['city'];
		$newTicket['Ticket']['userState']				 = $userData['Address'][0]['stateName'];
		$newTicket['Ticket']['userCountry']				 = $userData['Address'][0]['countryName'];
		$newTicket['Ticket']['userZip']					 = $userData['Address'][0]['postalCode'];

		$this->Ticket->create();
		if ($this->Ticket->save($newTicket)) {
			$this->updateTrackPending($this->Ticket->getLastInsertId());
			return true;	
		} else {
			$this->errorResponse = 908;
			return false;
		}
	}

	function updateTrackPending($ticketId) {
		if (!$ticketId) {
			return false;	
		}
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $id);
		$packageId = $ticket['Ticket']['packageId'];
		$this->ClientLoaPackageRel->recursive = 2;
		$clientLoaPackageRel = $this->ClientLoaPackageRel->findBypackageid($packageId);
		$revenueModelLoaRel = $clientLoaPackageRel['Loa']['RevenueModelLoaRel'][0];
		
		if (!empty($revenueModelLoaRel['revenueModelLoaRelId'])) {
			$revenueModelLoaRel['pending']+= $ticket['Ticket']['billingPrice'];
			$this->RevenueModelLoaRel->save($revenueModelLoaRel);
		}
	}
	
	function updateTrackDetail($in0) {
		$data = json_decode($in0, true);
		
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		
		$this->ClientLoaPackageRel->recursive = -1;
		$clientLoaPackageRel = $this->ClientLoaPackageRel->findBypackageid($data['packageId']);	
		
		$this->RevenueModelLoaRel->recursive = 2;
		$revenueModelLoaRel = $this->RevenueModelLoaRel->findByloaid($clientLoaPackageRel['ClientLoaPackageRel']['loaId']);
	
		$expCrit 			= $revenueModelLoaRel['ExpirationCriterium'];
		$revModel 			= $revenueModelLoaRel['RevenueModel'];
		$revModelLoa		= $revenueModelLoaRel['RevenueModelLoaRel'];
		$revModelLoaDetail 	= $revenueModelLoaRel['RevenueModelLoaRelDetail'];
	
		$revModelLoaDetail	= array();
	
		switch ($revModel['revenueModelId']) {
			case 1:
				if (empty($revModelLoaDetail)) {
					$revModelLoaDetail['revenueModelLoaRelId'] 	= $revModelLoa['revenueModelLoaId'];
					$revModelLoaDetail['ticketId'] 				= $data['ticketId'];
					$revModelLoaDetail['cycle'] 				= 1;
					$revModelLoaDetail['iteration'] 			= 1;
					$revModelLoaDetail['amountKept'] 			= ($revModelLoa['keepPercentage'] / 100) * $ticket['Ticket']['billingAmount'];
					$revModelLoaDetail['amountRemitted'] 		= $ticket['Ticket']['billingAmount'] - $revModelLoaDetail['amountKept']
					//$revModelLoaDetail[''] = 
				} else {
					
				}
				break;
			case 2:
				break;
			case 3:
				break;
		}


		return print_r($revModelLoaDetail, true);
		
	}
	
}
?>