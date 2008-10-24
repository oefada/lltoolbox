<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';
	var $uses = array('ticket','user');
	var $serviceUrl = 'http://192.168.100.111/web_service_tickets';
	var $errorResponse = 0;
	var $debugResponse = '';
	var $api = array(
					'requestProcessor1' => array(
						'doc' => 'Receive New Fixed Price',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					);

	function requestProcessor1($in0)
	{
		// all transmission via ESB is json encoded
		$request = json_decode($in0, true);
		$error = false;

		// these three variables are required so we can create the ticket
		if (!isset($request['requestId']) || !is_int($request['requestId'])) {
			$this->errorResponse = 901;
		} 		
		if (!isset($request['userId']) || !is_int($request['userId'])) {
			$this->errorResponse = 902;
		} 
		if (!isset($request['offerId']) || !is_int($request['offerId'])) {
			$this->errorResponse = 903;
		} 
		if (!isset($request['formatId']) || !is_int($request['formatId'])) {
			$this->errorResponse = 904;
		} 
		
		// there was an error so let's respond to the soap call with an error response 0 and init handleError()
		if ($error) {
			$request['response'] = $this->errorResponse;
			return json_encode($request);	
		} 

		// use the request data to attempt in creating a new ticket
		if ($this->createNewTicket($request)) {
			$request['response'] = 1;	
			
		} else {
			$request['response'] = $this->errorResponse;
		}
		
		return json_encode($request);
	}
	
	function handleError($errorCode) {
		
	}
	
	function createNewTicket($request) {
		if (empty($request) || !is_array($request)) {
			$this->errorResponse = 905;
			return false;	
		}
		
		$user = new User();
		$userData = $user->read(null, $request['userId']);
		
		$ticket = new Ticket();
		
		$newTicket = array();
		$newTicket['Ticket']['ticketStatusId'] 			= 1;
		$newTicket['Ticket']['packageId'] 				= $request['packageId'];
		$newTicket['Ticket']['offerId'] 				= $request['offerId'];
		$newTicket['Ticket']['offerTypeId'] 			= $request['offerTypeId'];
		$newTicket['Ticket']['formatId'] 				= $request['formatId'];
		$newTicket['Ticket']['bookingPrice'] 			= $request['bookingPrice'];
		$newTicket['Ticket']['ticketCreated'] 			= date('Y-m-d H:i:s');
		
		if ($request['formatId'] == 2) {
			$newTicket['Ticket']['requestId']     		= $request['requestId'];
			$newTicket['Ticket']['requestDate'] 		= $request['requestDate'];
			$newTicket['Ticket']['requestArrival'] 		= $request['requestArrival'];
			$newTicket['Ticket']['requestDeparture']	= $request['requestDeparture'];
			$newTicket['Ticket']['requestNumGuests']	= $request['requestNumGuests'];
			$newTicket['Ticket']['requestNotes']		= $request['requestNotes'];
		} else {
			$newTicket['Ticket']['bidId'] 				= $request['bidId'];
		}
		
		$newTicket['Ticket']['userId'] 					= $userData['User']['userId'];
		$newTicket['Ticket']['userFirstName'] 			= $userData['User']['firstName'];
		$newTicket['Ticket']['userLastName'] 			= $userData['User']['lastName'];
		$newTicket['Ticket']['userEmail1']				= $userData['User']['email'];
		$newTicket['Ticket']['userWorkPhone']			= $userData['User']['workPhone'];
		$newTicket['Ticket']['userHomePhone']			= $userData['User']['homePhone'];
		$newTicket['Ticket']['userMobilePhone']			= $userData['User']['mobilePhone'];
		$newTicket['Ticket']['userFax'] 				= $userData['User']['fax'];
		$newTicket['Ticket']['userAddress1']			= $userData['Address'][0]['address1'];
		$newTicket['Ticket']['userAddress2']			= $userData['Address'][0]['address2'];
		$newTicket['Ticket']['userAddress3']			= $userData['Address'][0]['address3'];
		$newTicket['Ticket']['userCity']				= $userData['Address'][0]['city'];
		$newTicket['Ticket']['userState']				= $userData['Address'][0]['stateName'];
		$newTicket['Ticket']['userCountry']				= $userData['Address'][0]['countryName'];
		$newTicket['Ticket']['userZip']					= $userData['Address'][0]['postalCode'];
		
		$this->debugResponse = $newTicket;
		
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