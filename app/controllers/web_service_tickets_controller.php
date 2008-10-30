<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';
	var $uses = array('ticket', 'user', 'offer', 'bid');
	var $serviceUrl = 'http://192.168.100.111/web_service_tickets';
	var $debugResponse = false;
	var $errorResponse = false;
	var $api = array(
					'newTicketProcessor1' => array(
						'doc' => 'Receive new requests or winning bids from the ESB and create a new ticket',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function newTicketProcessor1($in0)
	{
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if ($this->createNewTicket($json_decoded)) {
			$json_decoded['response'] = 1;
		} else {
			$json_decoded['response'] = $this->errorResponse;
		}
		/*
		if ($this->debugResponse) {
			return $this->debugResponse;
		}
		*/
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
		
		$user = new User();
		$userData = $user->read(null, $data['userId']);
		
		$offer = new Offer();
		$offer->recursive = -1;
		$offerData = $offer->read(null, $data['offerId']);
		$offerTypeToFormat = $offer->query("SELECT formatId FROM formatOfferTypeRel WHERE offerTypeId = " . $offerData['Offer']['offerTypeId']);
		$formatId = $offerTypeToFormat[0]['formatOfferTypeRel']['formatId'];
		
		if (isset($data['bidId']) && !empty($data['bidId'])) {
			$bid = new Bid();
			$bid->recursive = -1;
			$bidData = $bid->read(null, $data['bidId']);	
		}
	
		$newTicket = array();
		$newTicket['Ticket']['ticketStatusId'] 			 = 1;
		$newTicket['Ticket']['packageId'] 				 = $data['packageId'];
		$newTicket['Ticket']['offerId'] 				 = $data['offerId'];  
		$newTicket['Ticket']['offerTypeId'] 			 = $offerData['Offer']['offerTypeId'];
		$newTicket['Ticket']['formatId'] 				 = $formatId;
		$newTicket['Ticket']['ticketCreated'] 			 = date('Y-m-d H:i:s');
		
		if (isset($data['requestQueueId'])) {
			$newTicket['Ticket']['requestQueueId']     	 = $data['requestQueueId'];
			$newTicket['Ticket']['requestQueueDatetime'] = $data['requestQueueDatetime'];
			$newTicket['Ticket']['requestArrival'] 		 = $data['requestArrivalDate'];
			$newTicket['Ticket']['requestDeparture']	 = $data['requestDepartureDate'];
			$newTicket['Ticket']['requestNumGuests']	 = $data['requestNumGuests'];
			$newTicket['Ticket']['requestNotes']		 = $data['requestNotes'];
			$newTicket['Ticket']['bookingPrice'] 		 = $data['requestAmount'];
		} elseif (isset($data['bidId'])) {
			$newTicket['Ticket']['winningBidQueueId'] 	 = $data['winningBidQueueId'];
			$newTicket['Ticket']['bidId'] 				 = $data['bidId'];
			$newTicket['Ticket']['bookingPrice'] 		 = $bidData['Bid']['bidAmount'];
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
	
		//$this->debugResponse = print_r($newTicket, true);
		//return false;
			
		$ticket = new Ticket();
		$ticket->create();
		if ($ticket->save($newTicket)) {
			return true;	
		} else {
			$this->errorResponse = 906;
			return false;
		}
	}
}
?>