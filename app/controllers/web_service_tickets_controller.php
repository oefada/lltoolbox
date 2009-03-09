<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');
App::import('Vendor', 'aes.php');
require(APP.'/vendors/pp/Processor.class.php');  

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';
	var $uses = array('Ticket', 'UserPaymentSetting','PaymentDetail', 'Client', 'User', 'Offer', 'Bid', 'ClientLoaPackageRel', 'Track', 'OfferType', 'Loa', 'TrackDetail', 'PpvNotice', 'Address', 'Track', 'TrackDetail');
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_tickets';
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
						),
					'ppv' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendFromCheckout' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'processPaymentTicket' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'createFixedPriceTicket' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)	
					);
					
	function beforeFilter() { $this->LdapAuth->allow('*'); }

	function createFixedPriceTicket($in0) {
		// only used for creating fixed price tickets.
		// createNewTicket returns a ticket id for fixed price offers only so frontend can get ticket id
		// -------------------------------------------------------------------------------
		$json_decoded = json_decode($in0, true);
		$ticketId = $this->createNewTicket($json_decoded);
		
		// send out fixed price request emails
		// -------------------------------------------------------------------------------
		$params 					= array();
		$params['ticketId']			= $ticketId;
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'AUTO';
		
		// send out fixed price emails
		// -------------------------------------------------------------------------------
		$params['ppvNoticeTypeId'] = 9; 
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 10;
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 11;
		$this->ppv(json_encode($params));
		
		// return ticket id to the frontend live site
		// -------------------------------------------------------------------------------
		return $ticketId;
	}

	function newTicketProcessor1($in0)
	{
		
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->createNewTicket($json_decoded)) {			
			$json_decoded['response'] = $this->errorResponse;
			@mail('devmail@luxurylink.com','WEBSERVICE ERROR (TICKETS):  Cannot update ticket on toolboxprod-db', print_r($json_decoded, true), '-f devmail@luxurylink.com');
		} 
		return json_encode($json_decoded);
	}

	function createNewTicket($data) {

		// if we do not have these values then void
		// -------------------------------------------------------------------------------
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
		
		// gather all data for ticket creation
		// -------------------------------------------------------------------------------
		$this->User->recursive = -1;
		$userData = $this->User->read(null, $data['userId']);
		
		$this->Address->recursive = -1;
		$addressData = $this->Address->findByuserid($data['userId']);

		$this->Offer->recursive = 2;
		$offerData = $this->Offer->read(null, $data['offerId']);
		
		$this->Client->recursive = -1;
		$clientData = $this->Client->read(null, $data['clientId']);
		
		$offerTypeToFormat = $this->Offer->query("SELECT formatId FROM formatOfferTypeRel WHERE offerTypeId = " . $offerData['SchedulingInstance']['SchedulingMaster']['offerTypeId']);
		$formatId = $offerTypeToFormat[0]['formatOfferTypeRel']['formatId'];
		
		$offerLive = $this->Offer->query('SELECT * FROM offerLive WHERE offerId = ' . $data['offerId']);
		$offerLive = $offerLive[0]['offerLive'];
		
		// create a new ticket!
		// -------------------------------------------------------------------------------
		$newTicket = array();
		$newTicket['Ticket']['ticketStatusId'] 			 = 1;
		$newTicket['Ticket']['packageId'] 				 = $data['packageId'];
		$newTicket['Ticket']['clientId']				 = $data['clientId'];
		$newTicket['Ticket']['offerId'] 				 = $data['offerId'];  
		$newTicket['Ticket']['offerTypeId'] 			 = $offerData['SchedulingInstance']['SchedulingMaster']['offerTypeId'];
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
		$newTicket['Ticket']['userAddress1']			 = $addressData['Address']['address1'];
		$newTicket['Ticket']['userAddress2']			 = $addressData['Address']['address2'];
		$newTicket['Ticket']['userAddress3']			 = $addressData['Address']['address3'];
		$newTicket['Ticket']['userCity']				 = $addressData['Address']['city'];
		$newTicket['Ticket']['userState']				 = $addressData['Address']['stateName'];
		$newTicket['Ticket']['userCountry']				 = $addressData['Address']['countryName'];
		$newTicket['Ticket']['userZip']					 = $addressData['Address']['postalCode'];

		$this->Ticket->create();
		if ($this->Ticket->save($newTicket)) {

			$ticketId = $this->Ticket->getLastInsertId();

			// update the tracks
			// -------------------------------------------------------------------------------
			$schedulingMasterId = $offerData['SchedulingInstance']['SchedulingMaster']['schedulingMasterId'];
			$smid = $this->Track->query("select trackId from schedulingMasterTrackRel where schedulingMasterId = $schedulingMasterId limit 1");
			$smid = $smid[0]['schedulingMasterTrackRel']['trackId'];
			if (!empty($smid)) {
				$this->addTrackPending($smid, $newTicket['Ticket']['billingPrice']);
			}

			// if non-auction, just stop here as charging and ppv should not be auto
			// -------------------------------------------------------------------------------
			if ($formatId != 1 || !in_array($offerLive['offerTypeId'], array(1,2,6))) {
				return $ticketId;	
			}
			
			// find out if there is a valid credit card to charge.  charge and send appropiate emails
			// -------------------------------------------------------------------------------
			$user_payment_setting = $this->findValidUserPaymentSetting($userData['User']['userId']);
			
			// set ppv params
			// -------------------------------------------------------------------------------
			$ppv_settings = array();
			$ppv_settings['ticketId'] 			= $ticketId;
			$ppv_settings['send'] 				= 1;
			$ppv_settings['manualEmailBody']	= 0;
			$ppv_settings['returnString']		= 0;
			$ppv_settings['initials']			= 'AUTO';
			
			$auto_charge_card = false;
			if (is_array($user_payment_setting) && !empty($user_payment_setting)) {
				// has valid cc card to charge
				// -------------------------------------------
				$ppv_settings['ppvNoticeTypeId'] 	= 5;
				$auto_charge_card = true;
				
			} elseif ($user_payment_setting == 'EXPIRED') {
				// has valid cc card but is expired
				// -------------------------------------------
				$ppv_settings['ppvNoticeTypeId'] 	= 8;
				
			} else {
				// has no valid cc on file
				// -------------------------------------------
				$ppv_settings['ppvNoticeTypeId'] 	= 6;
				
			}

			// set restricted auctions so no autocharging happens
			// -------------------------------------------------------------------------------
			$restricted_auction = false;
			
			if ($offerLive['isMystery'] || $offerLive['retailValue'] == 1 || $offerLIve['openingBid'] == 1) {
				$restricted_auction = true;	
			}
			if ($clientData['Client']['clientTypeId'] == 3 || stristr($clientData['Client']['name'], 'CRUISE')) { 
            	$restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'RED') && stristr($offerLive['offerName'],'HOT')) {
            	$restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'FEATURED') && stristr($offerLive['offerName'],'AUCTION')) {
            	$restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'AUCTION') && stristr($offerLive['offerName'],'DAY')) {
            	$restricted_auction = true;
            }
             
 			// do no autocharge restricted auctions. send them old winner notification w/o checkout
 			// -------------------------------------------------------------------------------           
            if ($restricted_auction) {
            	$ppv_settings['ppvNoticeTypeId'] = 5;	
            	$auto_charge_card = false;
            }

			// check if user has already paid for this ticket
			// -------------------------------------------------------------------------------
			$checkExists = $this->PaymentDetail->query("SELECT * FROM paymentDetail WHERE ticketId = $ticketId");
			if (isset($checkExists[0]['paymentDetail']) && !empty($checkExists[0]['paymentDetail'])) {
				$auto_charge_card = false;
				$ppv_settings['ppvNoticeTypeId'] = 5;	
			}

			$autoSendClientWinnerPpv = false;
			// auto charge here
			// -------------------------------------------------------------------------------
			if (!$restricted_auction && $auto_charge_card) {
				$data_post = array();
		        $data_post['userId']                 = $userData['User']['userId'];
		        $data_post['ticketId']               = $ticketId;
		        $data_post['paymentProcessorId']     = $newTicket['Ticket']['billingPrice'] > 500 ? 1 : 4;
		        $data_post['paymentAmount']          = $newTicket['Ticket']['billingPrice'];
		        $data_post['initials']               = 'AUTOCHARGE';
		        $data_post['autoCharge']             = 1;
		        $data_post['saveUps']                = 0;
		        $data_post['zAuthHashKey']           = md5('L33T_KEY_LL' . $data_post['userId'] . $data_post['ticketId'] . $data_post['paymentProcessorId'] . $data_post['paymentAmount'] . $data_post['initials']);
				$data_post['userPaymentSettingId']	 = $user_payment_setting['UserPaymentSetting']['userPaymentSettingId'];
				
				$data_post_result = $this->processPaymentTicket(json_encode($data_post));
				if ($data_post_result == 'CHARGE_SUCCESS') {
					$ppv_settings['ppvNoticeTypeId'] = 5;	
					$autoSendClientWinnerPpv = true;
				} else {
					$ppv_settings['ppvNoticeTypeId'] = 7;
				}
			}
		
			// send out winner notifications
			// -------------------------------------------------------------------------------
			$this->ppv(json_encode($ppv_settings));
			
			// send out client and winner ppv if charge is successfully charged
			// -------------------------------------------------------------------------------
			if ($autoSendClientWinnerPpv) {
				$ppv_settings['ppvNoticeTypeId'] = 4; 
				$this->ppv(json_encode($ppv_settings));	
				
				$ppv_settings['ppvNoticeTypeId'] = 3;
				$this->ppv(json_encode($ppv_settings));	
			}
			
			return true;	
		} else {			
			// ticket was not succesfully created so send devmail alert
			// -------------------------------------------------------------------------------
			$this->errorResponse = 908;

			$errorBody = "DEBUG MESSAGE\n\n";
			$errorBody.= "--------------------------------\n\n";
			$errorBody.= "\nINPUT DATA:\n";
			$errorBody.= print_r($data, true);
			$errorBody.= "\nTICKET DATA CREATE ATTEMPT\n";
			$errorBody.= print_r($newTicket, true);
			$errorBody.= "\nUSER DATA\n";
			$errorbody.= print_r($userData, true);
			$errorBody.= "\nOFFER DATA\n";
			$errorBody.= print_r($offerData, true);
			$errorBody.= "\nUSER PAYMENT DATA\n";
			$errorBody.= print_r($user_payment_setting, true);
			
			$emailFrom = 'System<geeks@luxurylink.com>';
			$emailHeaders = "From: $emailFrom\r\n";
        	
			@mail('devmail@luxurylink.com', 'Ticketing Error - Failed to Create New Ticket', $errorBody, $emailHeaders, '-f devmail@luxurylink.com');

			return false;
		}
	}

	function autoSendFromCheckout($in0) {
		// from the frontend checkout, only ticketId comes in.  fill the rest for security
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'AUTO_USER_CHECKOUT';
		
		// send both the client and winner ppvs
		// -------------------------------------------------------------------------------
		$params['ppvNoticeTypeId'] = 4; 
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 3;
		$this->ppv(json_encode($params));	
	}

	function ppv($in0) {
		
		$params = json_decode($in0, true);
		
		// required params for sending and viewing ppvs
		// -------------------------------------------------------------------------------
		$ticketId 			= isset($params['ticketId']) ? $params['ticketId'] : null;
		$send 				= isset($params['send']) ? $params['send'] : false;
		$returnString 		= isset($params['returnString']) ? $params['returnString'] : false;
		$manualEmailBody	= isset($params['manualEmailBody']) ? $params['manualEmailBody'] : null;
		$ppvNoticeTypeId	= isset($params['ppvNoticeTypeId']) ? $params['ppvNoticeTypeId'] : null;
		$ppvInitials		= isset($params['initials']) ? $params['initials'] : null;
		
		// TODO: error checking for params
		
		// retrieve data to fill out the email templates
		// -------------------------------------------------------------------------------
			
		$this->Ticket->recursive = 0;
		$this->Address->recursive = -1;
		$this->ClientLoaPackageRel->recursive = 0;
		$ticket = $this->Ticket->read(null, $ticketId);
		$liveOffer	= $this->Ticket->query("select * from offerLive as LiveOffer where offerId = " . $ticket['Ticket']['offerId'] . " limit 1");
	
		// data arrays
		// -------------------------------------------------------------------------------
		$ticketData 		= $ticket['Ticket'];
		$packageData 		= $ticket['Package'];
		$offerData 			= $ticket['Offer'];
		$userData 			= $ticket['User'];
		$userAddressData	= $this->Address->findByuserid($userData['userId']);
		$userAddressData	= $userAddressData['Address'];
		$clientData			= $this->ClientLoaPackageRel->findAllBypackageid($ticket['Ticket']['packageId']);
		$liveOfferData 		= $liveOffer[0]['LiveOffer'];
		$offerType			= $this->OfferType->find('list');
		$userPaymentData	= $this->findValidUserPaymentSetting($ticketData['userId']);
		
		// vars for all email templates
		// -------------------------------------------------------------------------------
		$userId 			= $userData['userId'];
		$userFirstName		= ucwords(strtolower($userData['firstName']));
		$userLastName		= ucwords(strtolower($userData['lastName']));
		$emailName			= "$userFirstName $userLastName";
		$userEmail 			= $userData['email'];

		$userWorkPhone		= $userData['workPhone'];
		$userMobilePhone	= $userData['mobilePhone'];
		$userHomePhone		= $userData['homePhone'];
		
		$userPhone			= $userHomePhone;
		$userPhone			= !$userPhone && $userMobilePhone ? $userMobilePhone : $userPhone;
		$userPhone			= !$userPhone && $userWorkPhone ? $userWorkPhone : $userPhone;
		
		$offerId			= $offerData['offerId'];
		$packageName 		= strip_tags($liveOfferData['offerName']);
		$packageSubtitle	= $packageData['subtitle'];
		
		$packageIncludes 	= $packageData['packageIncludes'];
		$legalText			= $packageData['termsAndConditions'];
		$validityNote		= $packageData['validityDisclaimer'];
		
		$offerTypeId		= $ticketData['offerTypeId'];
		$offerTypeName		= $offerType[$offerTypeId];
		$offerTypeBidder	= ($offerTypeId == 1) ? 'Winner' : 'Winning Bidder';
		$offerEndDate		= date('M d Y H:i A', strtotime($liveOfferData['endDate']));
		$billingPrice		= number_format($ticketData['billingPrice'], 2, '.', ',');
		$llFeeAmount		= in_array($offerTypeId, array(1,2,6)) ? 30 : 40;
		$llFee				= number_format($llFeeAmount, 2, '.', ',');
		$totalPrice			= number_format(($ticketData['billingPrice'] + $llFeeAmount),  2, '.', ',');
		$maxNumWinners		= $liveOfferData['numWinners'];
		
		$checkoutHash		= md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
		$checkoutKey		= base64_encode(serialize(array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)));
		$checkoutLink		= "https://www.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
		
		$loaLevelId			= isset($clientData[0]['Loa']['loaLevelId']) ? $clientData[0]['Loa']['loaLevelId'] : false;
		
		$offerTypeArticle	= in_array(strtolower($offerType[$offerTypeId]{0}), array('a','e','i','o','u')) ? 'an' : 'a';
		
		// fixed price variables
		// -------------------------------------------------------------------------------
		$fpRequestType		= ($wholesale) ? 'A Wholesale Exclusive' : 'An Exclusive';
		$fpArrival			= isset($ticketData['requestArrival']) ? date('M d, Y', strtotime($ticketData['requestArrival'])) : 'N/A';
		$fpDeparture		= isset($ticketData['requestDeparture']) ? date('M d, Y', strtotime($ticketData['requestDeparture'])) : 'N/A';
		$fpNumGuests		= $ticketData['requestNumGuests'];
		$fpNotes			= $ticketData['requestNotes'];

		// cc variables
		// -------------------------------------------------------------------------------
		if (is_array($userPaymentData) && !empty($userPaymentData)) {
			$ccFour				= substr(aesDecrypt($userPaymentData['UserPaymentSetting']['ccNumber']), -4, 4);
			$ccType				= $userPaymentData['UserPaymentSetting']['ccType'];
		}
		
		// some unknowns
		// -------------------------------------------------------------------------------
		$guarantee			= false;
		$wholesale			= false;

		// fetch client contacts
		// -------------------------------------------------------------------------------
		$clients		 	= array();
		foreach ($clientData as $k => $v) {
			$tmp = $v['Client'];
			$tmp_result = $this->Ticket->query('SELECT * FROM clientContact WHERE clientContactTypeId = 1 and clientId = ' . $v['Client']['clientId'] . ' ORDER BY primaryContact DESC');
			foreach ($tmp_result as $a => $b) {
				$contacts = array();
				$contacts['ppv_name'] 			= $b['clientContact']['name'];
				$contacts['ppv_title'] 			= $b['clientContact']['businessTitle'];
				$contacts['ppv_email_address'] 	= $b['clientContact']['emailAddress']; 
				$contacts['ppv_phone'] 			= $b['clientContact']['phone'];
				$contacts['ppv_fax'] 			= $b['clientContact']['fax'];
				$tmp['contacts'][] = $contacts;
			}
			$clients[] = $tmp;
		}
		
		$clientId			= $clients[0]['clientId'];
		$clientNameP 		= $clients[0]['name'];
		$clientName 		= $clients[0]['contacts'][0]['ppv_name'];
		$clientPrimaryEmail = $clients[0]['contacts'][0]['ppv_email_address'];
		$oldProductId		= $clients[0]['oldProductId'];
		
		// build client CC list and check all email addresses
		// -------------------------------------------------------------------------------
		$countClientContact = count($clients[0]['contacts']);
		$clientCc			= array();
		if ($countClientContact) {
			for ($i = 1; $i < $countClientContact; $i++) {
				$clientCc[] = $clients[0]['contacts'][$i]['ppv_email_address'];
			}
		}
		$clientCcEmail = implode(',', array_unique($clientCc));
		
		// auction facilitator
		// -------------------------------------------------------------------------------
		// southern california - destinationId = 82
		// northern california - destinationId = 81
		// italy - destinationId = 6
		$auc_fac_result = $this->Ticket->query('SELECT count(*) AS auc_count FROM clientDestinationRel WHERE clientId = ' . $clientId . ' AND destinationId IN (6,81,82)');
		$is_auc_fac = ($auc_fac_result[0][0]['auc_count']) ? true : false;
		
		// auction facilitator overrides
		// -------------------------------------------------------------------------------
		$auc_fac_enable		= array(2138, 2811, 1826, 3401, 2692, 1205, 2076, 762, 3411, 2445, 3418, 654, 3401);
		$auc_fac_disable	= array(1013, 3253);
		if (in_array($clientId, $auc_fac_disable)) {
			$is_auc_fac = false;	
		}
		if (in_array($clientId, $auc_fac_enable)) {
			$is_auc_fac = true;	
		}

		// fetch template with the vars above
		// -------------------------------------------------------------------------------
		ob_start();
		switch ($ppvNoticeTypeId) {
			case 1:
				// send out res confirmation
				include('../vendors/email_msgs/ppv/conf_ppv.html');
				$emailSubject = "Luxury Link $offerTypeName Reservation Confirmation";
				$emailFrom = "LuxuryLink.com<reservations@luxurylink.com>";
				$emailReplyTo = "reservations@luxurylink.com";
				$emailBcc = 'thread@luxurylink.com';
				$returnPath = 'reservations@luxurylink.com';
				break;
			case 2:
				// send out res request
				include('../vendors/email_msgs/ppv/res_ppv.html');
				$emailSubject = "Luxury Link $offerTypeName Reservation Request";
				$emailFrom = "LuxuryLink.com<reservations@luxurylink.com>";
				$emailReplyTo = "reservations@luxurylink.com";
				$emailBcc = 'thread@luxurylink.com';
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				$returnPath = 'reservations@luxurylink.com';
				break;
			case 3:
				include('../vendors/email_msgs/ppv/winner_ppv.html');
				$emailSubject = "Luxury Link Package Purchase Verification - $packageName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = 'auction@luxurylink.com';
				$emailBcc = 'thread@luxurylink.com';
				$returnPath = 'auction@luxurylink.com';
				break;
			case 4: 
				include('../vendors/email_msgs/ppv/client_ppv.html');
				$emailSubject = "Luxury Link $offerTypeName Winner - $emailName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = 'auction@luxurylink.com';
				$emailBcc = 'thread@luxurylink.com';
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				$returnPath = 'auction@luxurylink.com';
				break;
			case 5:
				include('../vendors/email_msgs/notifications/winner_notification.html');
				$emailSubject = "Luxury Link $offerTypeName $offerTypeBidder - $packageName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				$emailBcc = 'winnernotifications@luxurylink.com';
				$returnPath = 'auction@luxurylink.com';
				break;
			case 6:
				include('../vendors/email_msgs/notifications/winner_notification_w_checkout.html');
				$emailSubject = "Luxury Link $offerTypeName $offerTypeBidder - $packageName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				$emailBcc = 'winnernotifications@luxurylink.com';
				$returnPath = 'auction@luxurylink.com';
				break;
			case 7:
				include('../vendors/email_msgs/notifications/winner_notification_decline_cc.html');
				$emailSubject = "Luxury Link $offerTypeName $offerTypeBidder - $packageName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				$emailBcc = 'winnernotifications@luxurylink.com';
				$returnPath = 'auction@luxurylink.com';
				break;
			case 8:
				include('../vendors/email_msgs/notifications/winner_notification_expired_cc.html');
				$emailSubject = "Luxury Link $offerTypeName $offerTypeBidder - $packageName";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				$emailBcc = 'winnernotifications@luxurylink.com';
				$returnPath = 'auction@luxurylink.com';
				break;
			case 9:
				include('../vendors/email_msgs/fixed_price/msg_fixedprice.html');
				$emailSubject = "LuxuryLink.com: Your Travel Request Has Been Received";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				$returnPath = 'exclusives@luxurylink.com';
				break;
			case 10:
				include('../vendors/email_msgs/fixed_price/msg_client_fixedprice.html');
				$emailSubject = "$fpRequestType Request Has Come In!";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				$returnPath = 'exclusives@luxurylink.com';
				break;
			case 11:
				include('../vendors/email_msgs/fixed_price/msg_internal_fixedprice.html');
				$emailSubject = "$fpRequestType Request Has Come In!";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				$userEmail = 'exclusives@luxurylink.com';
				$returnPath = 'exclusives@luxurylink.com';
				break;
			default:
				break;
		}
		$emailBody = ob_get_clean();

		// if sending from toolbox tool ppvNotice add screen (manual edit and send)
		// -------------------------------------------------------------------------------
		if ($manualEmailBody) {
			$emailBody = $manualEmailBody;
		}
	
		// send the email out!
		// -------------------------------------------------------------------------------
		if ($send) {
			$this->sendPpvEmail($userEmail, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials, $returnPath);	
		}
		
		// return the string for toolbox ppvNotice add screen (manual edit and send)
		// -------------------------------------------------------------------------------
		if ($returnString) {
			return $emailBody;	
		}
	}
		
	function sendPpvEmail($emailTo, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials, $returnPath) {
		
		// send out ppv and winner notification emails
		// -------------------------------------------------------------------------------
		$emailHeaders = "From: $emailFrom\r\n";
		$emailHeaders.= "Cc: $emailCc\r\n";
		$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
		$emailHeaders.= "Bcc: $emailBcc\r\n";
    	$emailHeaders.= "Content-type: text/html\r\n";
		
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders, "-f $returnPath");
		
		// below is for logging the email and updating the ticket
		// -------------------------------------------------------------------------------
		
		$emailSentDatetime = strtotime('now');
		$emailBodyFileName = $ticketId . '_' . $ppvNoticeTypeId . '_' . $emailSentDatetime . '.html';
		
		// save the email as a flat file on /vendors/email_msgs/toolbox_sent_messages
		// -------------------------------------------------------------------------------
		$fh = fopen("../vendors/email_msgs/toolbox_sent_messages/$emailBodyFileName", 'w');
		fwrite($fh, $emailBody);
		fclose($fh);
		
		// get initials
		// -------------------------------------------------------------------------------
		if (!$ppvInitials) {
			$ppvInitials = 'N/A';	
		}
		
		$ppvNoticeSave = array();
		$ppvNoticeSave['ppvNoticeTypeId']	= $ppvNoticeTypeId; 
		$ppvNoticeSave['ticketId'] 			= $ticketId;
		$ppvNoticeSave['emailTo']			= $emailTo;
		$ppvNoticeSave['emailFrom']			= $emailFrom;
		$ppvNoticeSave['emailCc']			= $emailCc;
		$ppvNoticeSave['emailSubject']		= $emailSubject;
		$ppvNoticeSave['emailBodyFileName']	= $emailBodyFileName;
		$ppvNoticeSave['emailSentDatetime']	= date('Y-m-d H:i:s', $emailSentDatetime);
		$ppvNoticeSave['initials']			= $ppvInitials;

		// save the record in the database
		// -------------------------------------------------------------------------------
		$this->PpvNotice->create();
		if (!$this->PpvNotice->save($ppvNoticeSave)) {
			@mail('devmail@luxurylink.com', 'WEB SERVICE TICKETS: ppv record not saved', print_r($ppvNoticeSave, true), '-f devmail@luxurylink.com');	
		}
		
		// update ticket status if required
		// -------------------------------------------------------------------------------
		$newTicketStatus = false;
		if ($ppvNoticeTypeId == 1) {
			$newTicketStatus = 4;		
		} elseif ($ppvNoticeTypeId == 2) {
			$newTicketStatus = 3;
		}
		if ($newTicketStatus) {
			$this->updateTicketStatus($ticketId, $newTicketStatus);
		}
	}
		
	function updateTicketStatus($ticketId, $newStatusId) {
		
		$updateTicket = array();
		$updateTicket['ticketId'] = $ticketId;
		$updateTicket['ticketStatusId'] = $newStatusId;
		if ($this->Ticket->save($updateTicket)) { 
			return 1;	
		} else {
			return 0;	
		}
	}
		
	function findValidUserPaymentSetting($userId) {
		
		$ups = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc");
		$year_now = date('Y');
		$month_now = date('m');
		if (empty($ups)) {
			return false;
		}
		$found_valid_cc = false;
		foreach ($ups as $k => $v) {
			if (($v['UserPaymentSetting']['expYear'] < $year_now) || ($v['UserPaymentSetting']['expYear'] == $year_now && $v['UserPaymentSetting']['expMonth'] < $month_now)) {
				continue;	
			} else {
				$found_valid_cc = true;
				break;
			}
		}
		return ($found_valid_cc) ? $v : 'EXPIRED';
	}

	function addTrackPending($trackId, $pendingAmount) {
		
		$track = $this->Track->read(null, $trackId);		
		if (!empty($track)) {
			$track['Track']['pending'] += $pendingAmount;
			if ($this->Track->save($track['Track'])) {
				return true;	
			}
		}
		return false;
	}
	
	function processPaymentTicket($in0) {
		// ---------------------------------------------------------------------------
		// SUBMIT PAYMENT VIA PROCESSOR 
		// ---------------------------------------------------------------------------
		// REQUIRED: (1) userId
		//           (2) ticketId
		//			 (3) paymentProcessorId
		// 			 (4) paymentAmount
		//           (5) initials
		//			 (6) autoCharge
		//           (7) saveUps
		//			 (8) zAuthHashKey
		//           (9) userPaymentSettingId or userPaymentSetting data array
		//           (10) toolboxManualCharge
		//
		// SEND TO PAYMENT PROCESSOR: $userPaymentSettingPost
		// ---------------------------------------------------------------------------
		
		// good o' error checking my friends.  make this as strict as possible
		// ---------------------------------------------------------------------------
		$data = json_decode($in0, true);

		if (!isset($data['userId']) || empty($data['userId'])) {
			return '101';
		}
		if (!isset($data['ticketId']) || empty($data['ticketId'])) {
			return '102';
		}
		if (!isset($data['paymentProcessorId']) || !$data['paymentProcessorId']) {
			return '103';	
		}
		if (!isset($data['paymentAmount']) || !$data['paymentAmount']) {
			return '104';	
		}
		if (!isset($data['initials']) || empty($data['initials'])) {
			return '105';	
		}
		if (!isset($data['autoCharge'])) {
			return '106';	
		}
		if (!isset($data['saveUps'])) { 
			return '107';	
		}
		if (!isset($data['zAuthHashKey']) || !$data['zAuthHashKey']) {
			return '108';	
		}
		if (isset($data['toolboxManualCharge']) && ($data['toolboxManualCharge'] == 'toolbox')) {
			$toolboxManualCharge = true;
		} else {
			$toolboxManualCharge = false;	
		}
		
		// also check the hash for more security
		// ---------------------------------------------------------------------------
		$hashCheck = md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);
		if (trim($hashCheck) !== trim($data['zAuthHashKey'])) {
			return '109';	
		}
		unset($hashCheck);
		
		// and even some more error checking.
		// ---------------------------------------------------------------------------
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		if (!$ticket) {
			return '110';
		} 
		if ($ticket['Ticket']['userId'] != $data['userId']) {
			return '111';
		}
		
		// use either the data sent over or retrieve from the db with the id
		// ---------------------------------------------------------------------------
		$userPaymentSettingPost = array();
		
		$usingUpsId = false;
		if (isset($data['userPaymentSettingId']) && !empty($data['userPaymentSettingId']) && is_numeric($data['userPaymentSettingId'])) {
			$tmp_result = $this->Ticket->query('SELECT * FROM userPaymentSetting WHERE userPaymentSettingId = ' . $data['userPaymentSettingId'] . ' LIMIT 1');
			$userPaymentSettingPost['UserPaymentSetting'] = $tmp_result[0]['userPaymentSetting'];
			unset($tmp_result);
			$usingUpsId = true;
		} else {
			$userPaymentSettingPost['UserPaymentSetting'] = $data['userPaymentSetting'];	
		}
		
		if (!$userPaymentSettingPost || empty($userPaymentSettingPost)) {
				return '113';
		}
		
		$userPaymentSettingPost['UserPaymentSetting']['ccNumber'] = aesFullDecrypt($userPaymentSettingPost['UserPaymentSetting']['ccNumber']);
		
		// set which processor to use
		// ---------------------------------------------------------------------------
		$paymentProcessorName = false;
		switch($data['paymentProcessorId']) {
			case 1:
				$paymentProcessorName = 'NOVA';
				break;
			case 3:
				$paymentProcessorName = 'PAYPAL';
				break;
			case 4:
				$paymentProcessorName = 'AIM';
				break;
			default:
				break;
		}
		
		if (!$paymentProcessorName) {
			return '114';	
		}
		
		if ($toolboxManualCharge) {
			$ticket['Ticket']['billingAmountOverride'] = $data['paymentAmount'];	
		}
		
		// init payment processing and submit payment
		// ---------------------------------------------------------------------------
		$processor = new Processor($paymentProcessorName);
		$processor->InitPayment($userPaymentSettingPost, $ticket);	
		$processor->SubmitPost();  

		// save the response from the payment processor
		// ---------------------------------------------------------------------------
		$nameSplit 								= str_word_count($userPaymentSettingPost['UserPaymentSetting']['nameOnCard'], 1);
		$firstName 								= trim($nameSplit[0]);
		$lastName 								= trim(array_pop($nameSplit));
		$userPaymentSettingPost['UserPaymentSetting']['expMonth'] = str_pad($userPaymentSettingPost['UserPaymentSetting']['expMonth'], 2, '0', STR_PAD_LEFT);
		
		$fee = in_array($ticket['Ticket']['offerTypeId'], array(1,2,6)) ? 30 : 40;
		$totalChargedAmount = ($toolboxManualCharge) ? $data['paymentAmount'] : $data['paymentAmount'] + $fee;
		
		$paymentDetail 							= array();
		$paymentDetail 							= $processor->GetMappedResponse();
		$paymentDetail['paymentTypeId'] 		= 1; 
		$paymentDetail['paymentAmount']			= $data['paymentAmount'];
		$paymentDetail['ticketId']				= $ticket['Ticket']['ticketId'];
		$paymentDetail['userId']				= $ticket['Ticket']['userId'];
		$paymentDetail['userPaymentSettingId']	= ($usingUpsId) ? $data['userPaymentSettingId'] : '';
		$paymentDetail['paymentProcessorId']	= $data['paymentProcessorId'];
		$paymentDetail['ppFirstName']			= $firstName;
		$paymentDetail['ppLastName']			= $lastName;
		$paymentDetail['ppBillingAddress1']		= $userPaymentSettingPost['UserPaymentSetting']['address1'];
		$paymentDetail['ppBillingCity']			= $userPaymentSettingPost['UserPaymentSetting']['city'];
		$paymentDetail['ppBillingState']		= $userPaymentSettingPost['UserPaymentSetting']['state'];
		$paymentDetail['ppBillingZip']			= $userPaymentSettingPost['UserPaymentSetting']['postalCode'];
		$paymentDetail['ppBillingCountry']		= $userPaymentSettingPost['UserPaymentSetting']['country'];
		$paymentDetail['ppCardNumLastFour']		= substr($userPaymentSettingPost['UserPaymentSetting']['ccNumber'], -4, 4);
		$paymentDetail['ppExpMonth']			= $userPaymentSettingPost['UserPaymentSetting']['expMonth'];
		$paymentDetail['ppExpYear']				= $userPaymentSettingPost['UserPaymentSetting']['expYear'];
		$paymentDetail['ppBillingAmount']		= $totalChargedAmount;
		$paymentDetail['autoProcessed']			= $data['autoCharge'];
		$paymentDetail['initials']				= $data['initials'];
		$paymentDetail['ccType']				= $userPaymentSettingPost['UserPaymentSetting']['ccType'];

		$this->PaymentDetail->create();
		if (!$this->PaymentDetail->save($paymentDetail)) {
			@mail('devmail@luxurylink.com', 'WEB SERVICE ERROR: PAYMENT PROCESSED BUT NOT SAVED', print_r($this->PaymentDetail->validationErrors,true)  . print_r($paymentDetail, true), '-f devmail@luxurylink.com');
		}
				
		// return result whether success or denied
		// ---------------------------------------------------------------------------
		if ($processor->ChargeSuccess()) {
			
			// allocate revenue to loa and tracks
			// ---------------------------------------------------------------------------
			$track = $this->TrackDetail->getTrackRecord($ticket['Ticket']['ticketId']);
			if ($track) {
				$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $ticket['Ticket']['ticketId']);	
				if (!$trackDetailExists) {
					$new_track_detail = $this->TrackDetail->getNewTrackDetailRecord($track, $ticket['Ticket']['ticketId']);
					if ($new_track_detail) {
						$this->TrackDetail->create();
						$this->TrackDetail->save($new_track_detail);
					}
				}
			}
			
			// if saving new user card information
			// ---------------------------------------------------------------------------
			if ($data['saveUps'] && !$usingUpsId && !empty($userPaymentSettingPost['UserPaymentSetting'])) {
				$this->UserPaymentSetting->create();
				$this->UserPaymentSetting->save($userPaymentSettingPost['UserPaymentSetting']);
			}
			
			$ticketStatusChange = array();
			$ticketStatusChange['ticketId'] = $ticket['Ticket']['ticketId'];
			$ticketStatusChange['ticketStatusId'] = 5;
			$this->Ticket->save($ticketStatusChange);
			
			return 'CHARGE_SUCCESS';
		} else {
			return $processor->GetResponseTxt();
		}
	}
}
?>