<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');
App::import('Vendor', 'aes.php');
require(APP.'/vendors/pp/Processor.class.php');  

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';

	var $uses = array('Ticket', 'UserPaymentSetting','PaymentDetail', 'Client', 'User', 'Offer', 'Bid', 
					  'ClientLoaPackageRel', 'Track', 'OfferType', 'Loa', 'TrackDetail', 'PpvNotice',
					  'Address', 'OfferLive', 'SchedulingMaster', 'SchedulingInstance', 'Reservation',
					  'PromoTicketRel', 'Promo', 'TicketReferFriend'
					  );

	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_tickets';
	var $serviceUrlDev = 'http://toolboxdev.luxurylink.com/web_service_tickets';
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
					'autoSendPreferredDates' => array(
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

		if (!$ticketId) {
			return false;
		}
		
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
		$params['ppvNoticeTypeId'] = 9;     // Fixed Price - Winner Notification
		$this->ppv(json_encode($params));	
		
		if (trim($json_decoded['requestNotes'])) {
			$params['ppvNoticeTypeId'] = 10;     // Fixed Price - Client Exclusive Email
		} else {
			$params['ppvNoticeTypeId'] = 2;     // Reservation Request
		}
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 11;     // Fixed Price - Internal Exclusive Email
		$this->ppv(json_encode($params));
		
		// return ticket id to the frontend live site
		// -------------------------------------------------------------------------------
		return $ticketId;
	}

	function newTicketProcessor1($in0) {
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->createNewTicket($json_decoded)) {			
			$json_decoded['response'] = $this->errorResponse;
			@mail('devmail@luxurylink.com','WEBSERVICE ERROR (TICKETS):  Cannot update ticket on toolboxprod-db', print_r($json_decoded, true));
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
		
		$offerTypeToFormat = $this->Offer->query("SELECT formatId FROM formatOfferTypeRel WHERE offerTypeId = " . $offerData['SchedulingInstance']['SchedulingMaster']['offerTypeId']);
		$formatId = $offerTypeToFormat[0]['formatOfferTypeRel']['formatId'];
		
		$offerLive = $this->Offer->query('SELECT * FROM offerLive WHERE offerId = ' . $data['offerId']);
		$offerLive = $offerLive[0]['offerLive'];
		
		$clientData = $this->Ticket->getClientsFromPackageId($data['packageId']);
		
		// create a new ticket!
		// -------------------------------------------------------------------------------
		$newTicket = array();
		$newTicket['Ticket']['ticketStatusId'] 			 = 1;
		$newTicket['Ticket']['packageId'] 				 = $data['packageId'];
		$newTicket['Ticket']['offerId'] 				 = $data['offerId'];  
		$newTicket['Ticket']['offerTypeId'] 			 = $offerData['SchedulingInstance']['SchedulingMaster']['offerTypeId'];
		$newTicket['Ticket']['formatId'] 				 = $formatId;
		
		if (isset($data['requestQueueId'])) {
			$newTicket['Ticket']['requestQueueId']     	 = $data['requestQueueId'];
			$newTicket['Ticket']['requestQueueDatetime'] = $data['requestQueueDatetime'];
			$newTicket['Ticket']['requestArrival'] 		 = $data['requestArrival'];
			$newTicket['Ticket']['requestDeparture']	 = $data['requestDeparture'];
			$newTicket['Ticket']['requestArrival2'] 	 = $data['requestArrival2'];
			$newTicket['Ticket']['requestDeparture2']	 = $data['requestDeparture2'];
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
			
			// take down future instances of offers if reached package.maxNumSales
			// -------------------------------------------------------------------------------
			$this->Ticket->__runTakeDownPackageNumPackages($data['packageId'], $ticketId);
			$this->Ticket->__runTakeDownLoaMemBal($data['packageId'], $ticketId, $data['billingPrice']);
			$this->Ticket->__runTakeDownLoaNumPackages($data['packageId'], $ticketId);

			// find and set promos for this new ticket
			// -------------------------------------------------------------------------------
			$promo_data = $this->Ticket->findPromoOfferTrackings($data['userId'], $data['offerId']);
			if ($promo_data !== false && is_array($promo_data) && !empty($promo_data)) {
				foreach ($promo_data as $promoOfferTracking) {
					$promo_ticket_rel = array();
					$promo_ticket_rel['promoCodeId'] = $promoOfferTracking['promoOfferTracking']['promoCodeId'];
					$promo_ticket_rel['ticketId'] = $ticketId;
					$promo_ticket_rel['userId'] = $data['userId'];
					$this->PromoTicketRel->create();
					if ($this->PromoTicketRel->save($promo_ticket_rel)) {
						$referrerUserId = $this->Promo->checkInsertRaf($promo_ticket_rel['promoCodeId']);
						if ($referrerUserId !== false && is_numeric($referrerUserId)) {
							$ticket_refer_friend = array();
							$ticket_refer_friend['ticketId'] = $ticketId;
							$ticket_refer_friend['referrerUserId'] = $referrerUserId;
							$ticket_refer_friend['datetime'] = date('Y:m:d H:i:s', strtotime('now'));
							$this->TicketReferFriend->save($ticket_refer_friend);
						}
					}
				}
			}

			// if non-auction, just stop here as charging and ppv should not be auto
			// -------------------------------------------------------------------------------
			if ($formatId != 1 || !in_array($offerLive['offerTypeId'], array(1,2,6))) {
				return $ticketId;	
			}
			
			// find out if there is a valid credit card to charge.  charge and send appropiate emails
			// -------------------------------------------------------------------------------
			$user_payment_setting = $this->findValidUserPaymentSetting($userData['User']['userId'], $data['bidId']);
			
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
				$ppv_settings['ppvNoticeTypeId'] 	= 18;     // Auction Winner Email (PPV)
				$auto_charge_card = true;
			} else {
				// has no valid cc on file
				// -------------------------------------------
				$ppv_settings['ppvNoticeTypeId'] 	= 19;     // Auction Winner Email (Declined / Expired CC) 
			}

			// set restricted auctions so no autocharging happens
			// -------------------------------------------------------------------------------
			$restricted_auction = false;
			
			foreach ($clientData as $client) {
				if ($client['Client']['clientTypeId'] == 3 || stristr($client['Client']['name'], 'CRUISE')) { 
            		$restricted_auction = true;
            	}	
			}
			if ($offerLive['isMystery'] || $offerLive['retailValue'] == 1 || $offerLIve['openingBid'] == 1) {
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
            	$ppv_settings['ppvNoticeTypeId'] = 5;     // Winner Notification (Old one)
            	$auto_charge_card = false;
            }

			// check if user has already paid for this ticket
			// -------------------------------------------------------------------------------
			$checkExists = $this->PaymentDetail->query("SELECT * FROM paymentDetail WHERE ticketId = $ticketId");
			if (isset($checkExists[0]['paymentDetail']) && !empty($checkExists[0]['paymentDetail'])) {
				$auto_charge_card = false;
				$ppv_settings['ppvNoticeTypeId'] = 18;     // Auction Winner Email (PPV)
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
					$ppv_settings['ppvNoticeTypeId'] = 18;     // Auction Winner Email (PPV)
					$autoSendClientWinnerPpv = true;
				} else {
					$ppv_settings['ppvNoticeTypeId'] = 19;     // Auction Winner Email (Declined / Expired CC) 
				}
			}
		
			// send out winner notifications
			// -------------------------------------------------------------------------------
			$this->ppv(json_encode($ppv_settings));
			
			// send out client and winner ppv if charge is successfully charged
			// -------------------------------------------------------------------------------
			if ($autoSendClientWinnerPpv) {
				$ppv_settings['ppvNoticeTypeId'] = 4;    // client PPV
				$this->ppv(json_encode($ppv_settings));	
			}
			
			// finally, return back
			// -------------------------------------------------------------------------------
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
        	
			@mail('devmail@luxurylink.com', 'Ticketing Error - Failed to Create New Ticket', $errorBody, $emailHeaders);

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
		$params['ppvNoticeTypeId'] = 4;    // client PPV
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 18;    // Auction Winner Email (PPV)
		$this->ppv(json_encode($params));	
	}

	function autoSendPreferredDates($in0) {
		// from the frontend my dates request
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'AUTO_USER_DATES';
		
		// send both the my dates have been received and reservation request
		// -------------------------------------------------------------------------------
		$params['ppvNoticeTypeId'] = 20;     // Your Dates Have Been Received
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 2;    // Reservation Request
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
		$clientIdParam		= isset($params['clientId']) ? $params['clientId'] : false;
		
		// sender signature (mainly for manual emails sent from toolbox)
		// -------------------------------------------------------------------------------
		$sender_sig 		= isset($params['sender_sig']) ? $params['sender_sig'] : 0;
		$sender_sig_line	= isset($params['sender_sig_line']) ? $params['sender_sig_line'] : '';
		$sender_email		= isset($params['sender_email']) ? $params['sender_email'] : '';
		$sender_ext			= isset($params['sender_ext']) ? $params['sender_ext'] : '';

		// override the to and cc fields from toolbox manual send
		// -------------------------------------------------------------------------------
		$override_email_to  = isset($params['override_email_to']) && !empty($params['override_email_to']) ? $params['override_email_to'] : false;
		$override_email_cc  = isset($params['override_email_cc']) && !empty($params['override_email_cc']) ? $params['override_email_cc'] : false;

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

		$promoGcCofData		= $this->Ticket->getPromoGcCofData($ticketId, $ticket['Ticket']['billingPrice']);

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
		
		$packageIncludes 	= $liveOfferData['offerIncludes'];
		$legalText			= $liveOfferData['termsAndConditions'];
		$validityNote		= $liveOfferData['validityDisclaimer'];
		$validityLeadIn     = $packageData['validityLeadInLine'];
		$addtlDescription   = $liveOfferData['additionalDescription'];
		
		$packageId			= $ticketData['packageId'];

		$offerTypeId		= $ticketData['offerTypeId'];
		$offerTypeName		= str_replace('Standard ', '', $offerType[$offerTypeId]);
		$offerTypeBidder	= ($offerTypeId == 1) ? 'Winner' : 'Winning Bidder';
		$offerEndDate		= date('M d Y H:i A', strtotime($liveOfferData['endDate']));
		$isAuction			= in_array($offerTypeId, array(1,2,6)) ? true : false;

		$billingPrice		= $ticketData['billingPrice'];
		$llFeeAmount		= $this->Ticket->getFeeByTicket($ticketId);
		$llFee				= $llFeeAmount;
		$totalPrice			= $ticketData['billingPrice'] + $llFeeAmount;
		$maxNumWinners		= $liveOfferData['numWinners'];
		
		$checkoutHash		= md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
		$checkoutKey		= base64_encode(serialize(array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)));
		$checkoutLink		= "https://www.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$checkoutLink		= "https://livedev.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
		}
		
		$loaLevelId			= isset($clientData[0]['Loa']['loaLevelId']) ? $clientData[0]['Loa']['loaLevelId'] : false;
		
		$offerTypeArticle	= in_array(strtolower($offerType[$offerTypeId]{0}), array('a','e','i','o','u')) ? 'an' : 'a';

		// fixed price variables
		// -------------------------------------------------------------------------------
		$fpRequestType		= ($wholesale) ? 'A Wholesale Exclusive' : 'An Exclusive';
		$fpArrival			= isset($ticketData['requestArrival']) ? date('M d, Y', strtotime($ticketData['requestArrival'])) : 'N/A';
		$fpDeparture		= isset($ticketData['requestDeparture']) ? date('M d, Y', strtotime($ticketData['requestDeparture'])) : 'N/A';
		$fpArrival2			= isset($ticketData['requestArrival2']) && ($ticketData['requestArrival2'] != '0000-00-00') ? date('M d, Y', strtotime($ticketData['requestArrival2'])) : 'N/A';
		$fpDeparture2		= isset($ticketData['requestDeparture2']) && ($ticketData['requestDeparture2'] != '0000-00-00') ? date('M d, Y', strtotime($ticketData['requestDeparture2'])) : 'N/A';
		$fpNumGuests		= $ticketData['requestNumGuests'];
		$fpNotes			= $ticketData['requestNotes'];
		
		// auction preferred dates
		// -------------------------------------------------------------------------------
		$aucPreferDates = $this->Ticket->query("SELECT * FROM reservationPreferDate as rpd WHERE ticketId = $ticketId ORDER BY reservationPreferDateTypeId");	
		if (!empty($aucPreferDates)) {
			foreach ($aucPreferDates as $aucKey => $aucPreferDateRow) {
				$aucPreferDates[$aucKey]['rpd']['in'] = date('M d, Y', strtotime($aucPreferDateRow['rpd']['arrivalDate'])); 		
				$aucPreferDates[$aucKey]['rpd']['out'] = date('M d, Y', strtotime($aucPreferDateRow['rpd']['departureDate'])); 		
			}
		}

		if (!$isAuction && !empty($aucPreferDates)) {
			$fpArrival			= ($aucPreferDates[0]['rpd']['in']) ? $aucPreferDates[0]['rpd']['in'] : 'N/A';
			$fpDeparture		= ($aucPreferDates[0]['rpd']['out']) ? $aucPreferDates[0]['rpd']['out'] : 'N/A';
			$fpArrival2			= ($aucPreferDates[1]['rpd']['in']) ? $aucPreferDates[1]['rpd']['in'] : 'N/A';
			$fpDeparture2		= ($aucPreferDates[1]['rpd']['out']) ? $aucPreferDates[1]['rpd']['out'] : 'N/A';
			$fpArrival3			= ($aucPreferDates[2]['rpd']['in']) ? $aucPreferDates[2]['rpd']['in'] : 'N/A';
			$fpDeparture3		= ($aucPreferDates[2]['rpd']['out']) ? $aucPreferDates[2]['rpd']['out'] : 'N/A';
		}

		if ($ppvNoticeTypeId == 1) {
			// for reservation confirmation
			$resData = $this->Ticket->query("SELECT * FROM reservation WHERE ticketId = $ticketId");
			if (!empty($resData)) {
				$resConfNum = $resData[0]['reservation']['reservationConfirmNum'];
				$resArrivalDate = date('M d, Y', strtotime($resData[0]['reservation']['arrivalDate']));
				$resDepartureDate = date('M d, Y', strtotime($resData[0]['reservation']['departureDate']));
			}
		}

		// auction facilitator
		// -------------------------------------------------------------------------------
		$dateRequestLink = "https://www.luxurylink.com/my/my_date_request.php?tid=$ticketId";
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$dateRequestLink = "https://livedev.luxurylink.com/my/my_date_request.php?tid=$ticketId";
		}

		// for MasterCard sponsor only
		// -------------------------------------------------------------------------------
		$mcPromo = false;
		if (isset($promoGcCofData['Promo']) && (stristr($promoGcCofData['Promo']['promoCode'], 'LLMCWORLD09'))) {
			if ($this->Ticket->__isValidPackagePromo(1, $packageId)) {
				$mcPromo = true;							
			}
		}

		// cc variables
		// -------------------------------------------------------------------------------
		if (is_array($userPaymentData) && !empty($userPaymentData)) {
			$ccFour				= substr(aesDecrypt($userPaymentData['UserPaymentSetting']['ccNumber']), -4, 4);
			$ccType				= $userPaymentData['UserPaymentSetting']['ccType'];
		}
		
		// guarantee amount
		// -------------------------------------------------------------------------------
		$guarantee = false;
		if ($packageData['reservePrice'] && is_numeric($packageData['reservePrice']) && ($packageData['reservePrice'] > 0)) {
			if ($ticketData['billingPrice'] < $packageData['reservePrice']) {
				$guarantee = $packageData['reservePrice'];
			}
		}
		
		// some unknowns
		// -------------------------------------------------------------------------------
		$wholesale			= false;

		// fetch client contacts
		// -------------------------------------------------------------------------------
		$clients		 	= array();
		$multi_client_map_override = false;
		foreach ($clientData as $k => $v) {
			$tmp = $v['Client'];
			if ($clientIdParam && ($clientIdParam == $tmp['clientId'])) {
				$multi_client_map_override = $k;
			}
			if (!empty($v['Client']['parentClientId']) && is_numeric($v['Client']['parentClientId']) && ($v['Client']['parentClientId'] > 0) && ($v['Client']['clientId'] != $v['Client']['parentClientId'])) {
				$add_parent_client_sql = "OR clientId = " . $v['Client']['parentClientId'];
			} else {
				$add_parent_client_sql = '';	
			}
			$tmp_result = $this->Ticket->query("SELECT * FROM clientContact WHERE clientContactTypeId in (1,3) AND (clientId = " . $v['Client']['clientId'] . " $add_parent_client_sql) ORDER BY clientContactTypeId, primaryContact DESC");
			$contact_cc_string = array();
			$contact_to_string = array();
			foreach ($tmp_result as $a => $b) {
				$contacts = array();
				$contacts['ppv_name'] 			= $b['clientContact']['name'];
				$contacts['ppv_title'] 			= $b['clientContact']['businessTitle'];
				$contacts['ppv_email_address'] 	= $b['clientContact']['emailAddress']; 
				$contacts['ppv_phone'] 			= $b['clientContact']['phone'];
				$contacts['ppv_fax'] 			= $b['clientContact']['fax'];
				if ($b['clientContact']['clientContactTypeId'] == 1) {
					$contact_to_string[] = $b['clientContact']['emailAddress'];
				}
				if ($b['clientContact']['clientContactTypeId'] == 3) {
					$contact_cc_string[] = $b['clientContact']['emailAddress'];
				}
				$tmp['contacts'][] = $contacts;
			}
			$tmp['contact_cc_string'] = implode(',', array_unique($contact_cc_string));
			$tmp['contact_to_string'] = implode(',', array_unique($contact_to_string));
			if (!$tmp['contact_to_string'] && !empty($tmp['contact_cc_string'])) {
				$tmp['contact_to_string'] = $tmp['contact_cc_string'];
				$tmp['contact_cc_string'] = '';
			}
			$tmp['percentOfRevenue'] = $v['ClientLoaPackageRel']['percentOfRevenue'];
			$clients[$k] = $tmp;
		}
		
		$client_index = ($multi_client_map_override !== false) ? $multi_client_map_override : 0;
		
		$clientId			= $clients[$client_index]['clientId'];
		$clientNameP 		= $clients[$client_index]['name'];
		$clientName 		= $clients[$client_index]['contacts'][0]['ppv_name'];
		$oldProductId		= $clients[$client_index]['oldProductId'];
		$locationDisplay	= $clients[$client_index]['locationDisplay'];
		$clientPrimaryEmail = $clients[$client_index]['contact_to_string'];
		$clientCcEmail 		= $clients[$client_index]['contact_cc_string'];
		$clientAdjustedPrice = ($clients[$client_index]['percentOfRevenue'] / 100) * $ticketData['billingPrice'];
		
		// fetch template with the vars above
		// -------------------------------------------------------------------------------
		ob_start();
		switch ($ppvNoticeTypeId) {
			case 1:
				// send out res confirmation
				include('../vendors/email_msgs/ppv/conf_ppv.html');
				$emailSubject = "Your Luxury Link Booking is Confirmed - $clientNameP";
				$emailFrom = ($isAuction) ? "Luxurylink.com<resconfirm@luxurylink.com>" : "LuxuryLink.com<reservations@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "resconfirm@luxurylink.com" : "reservations@luxurylink.com";
				break;
			case 2:
				// send out res request
				include('../vendors/email_msgs/ppv/res_ppv.html');
				$emailSubject = "Please Confirm This Luxury Link Booking - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "Luxurylink.com<resrequests@luxurylink.com>" : "LuxuryLink.com<reservations@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "resrequests@luxurylink.com" : "reservations@luxurylink.com";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 4: 
				include('../vendors/email_msgs/ppv/client_ppv.html');
				$emailSubject = "Luxury Link Auction Winner Notification - $userFirstName $userLastName";
				$emailFrom = "LuxuryLink.com<auctions@luxurylink.com>";
				$emailReplyTo = 'auctions@luxurylink.com';
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 5:
				include('../vendors/email_msgs/notifications/winner_notification.html');
				$emailSubject = "Luxury Link Auction Winner - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 9:
				include('../vendors/email_msgs/fixed_price/msg_fixedprice.html');
				$emailSubject = "Luxury Link - Your Request Has Been Received";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				break;
			case 10:
				include('../vendors/email_msgs/fixed_price/msg_client_fixedprice.html');
				$emailSubject = "An Exclusive Luxury Link Booking Request Has Come In!";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 11:
				include('../vendors/email_msgs/fixed_price/msg_internal_fixedprice.html');
				$emailSubject = "$fpRequestType Request Has Come In!";
				$emailFrom = "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = "exclusives@luxurylink.com";
				$userEmail = 'exclusives@luxurylink.com';
				break;
			case 12:
				include('../vendors/email_msgs/fixed_price/notification_acknowledgement.html');
				$emailSubject = "Your Luxury Link Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "Luxurylink.com<auction@luxurylink.com>" : "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "auction@luxurylink.com" : "exclusives@luxurylink.com";
				break;
			case 13:
				include('../vendors/email_msgs/fixed_price/notification_dates_available.html');
				$emailSubject = "Your Luxury Link Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "Luxurylink.com<auction@luxurylink.com>" : "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "auction@luxurylink.com" : "exclusives@luxurylink.com";
				break;
			case 14:
				include('../vendors/email_msgs/fixed_price/notification_dates_not_available.html');
				$emailSubject = "Your Luxury Link Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "Luxurylink.com<auction@luxurylink.com>" : "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "auction@luxurylink.com" : "exclusives@luxurylink.com";
				break;
			case 15:
				include('../vendors/email_msgs/notifications/chase_money_notification.html');
				$emailSubject = "Luxury Link Auction Winner - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 16:
				include('../vendors/email_msgs/notifications/first_offense_flake.html');
				$emailSubject = "Luxury Link Auction Winner - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 17:
				include('../vendors/email_msgs/notifications/second_offense_flake.html');
				$emailSubject = "Luxury Link Auction Winner - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 18:
				include('../vendors/email_msgs/notifications/18_auction_winner_ppv.html');
				$emailSubject = "Luxury Link Auction Winner Receipt - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 19:
				include('../vendors/email_msgs/notifications/19_auction_winner_declined_expired.html');
				$emailSubject = "Luxury Link Auction Winner Notification - $clientNameP";
				$emailFrom = "LuxuryLink.com<auction@luxurylink.com>";
				$emailReplyTo = "auction@luxurylink.com";
				break;
			case 20:
				include('../vendors/email_msgs/notifications/20_auction_your_dates_received.html');
				$emailSubject = "Your Request has been Received - $clientNameP";
				$emailFrom = ($isAuction) ? "Luxurylink.com<auction@luxurylink.com>" : "LuxuryLink.com<exclusives@luxurylink.com>";
				$emailReplyTo = ($isAuction) ? "auction@luxurylink.com" : "exclusives@luxurylink.com";
				break;
			default:
				break;
		}
		$emailBody = ob_get_clean();

		if (in_array($ppvNoticeTypeId, array(5,18,19)) && $liveOfferData['isMystery']) {
			$emailSubject = "Luxury Link Mystery Auction Winner";
		}
		
		// if sending from toolbox tool ppvNotice add screen (manual edit and send)
		// -------------------------------------------------------------------------------
		if ($manualEmailBody) {
			$emailBody = $manualEmailBody;
		}

		// send the email out!
		// -------------------------------------------------------------------------------
		if ($send) {
			if (trim($override_email_to)) {
				$userEmail = $override_email_to;
			}
			if (trim($override_email_cc)) {
				$emailCc = $override_email_cc;
			}
			$this->sendPpvEmail($userEmail, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials);	
			
			// AUTO SECTION FOR MULTI CLIENT PPV for multi-client packages send client emails [CLIENT PPV]
			// -------------------------------------------------------------------------------
			$count_clients = count($clients);
			if ((in_array($ppvNoticeTypeId, array(2,4,10))) && (!$manualEmailBody) && ($count_clients > 1)) {
				for ($i = 1; $i < $count_clients; $i++) {
					$clientId			= $clients[$i]['clientId'];
					$clientNameP 		= $clients[$i]['name'];
					$clientName 		= $clients[$i]['contacts'][0]['ppv_name'];
					$oldProductId		= $clients[$i]['oldProductId'];
					$locationDisplay	= $clients[$i]['locationDisplay'];
					$clientPrimaryEmail = $clients[$i]['contact_to_string'];
					$clientCcEmail 		= $clients[$i]['contact_cc_string'];	
					$clientAdjustedPrice = ($clients[$i]['percentOfRevenue'] / 100) * $ticketData['billingPrice'];
					ob_start();
					switch ($ppvNoticeTypeId) {
						case 2:
							include('../vendors/email_msgs/ppv/res_ppv.html');
							break;
						case 4:
							include('../vendors/email_msgs/ppv/client_ppv.html');
							break;
						case 10:
							include('../vendors/email_msgs/fixed_price/msg_client_fixedprice.html');
							break;
					}
					$emailBody = ob_get_clean();
					$userEmail = $clientPrimaryEmail;
					$emailCc = $clientCcEmail;
					$this->sendPpvEmail($userEmail, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials);	
				}	
			}
		}
		
		// return the string for toolbox ppvNotice add screen (manual edit and send)
		// -------------------------------------------------------------------------------
		if ($returnString) {
			return $emailBody;	
		}
	}

	function sendPpvEmail($emailTo, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials) {
		
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$appendDevMessage = "---- DEV MAIL ---- \n<br />ORIGINAL TO:  $emailTo\n<br />ORIGINAL CC: $emailCc\n<br />ORIGINAL BCC: $emailBcc";
			$emailTo = $emailCc = $emailBcc = 'devmail@luxurylink.com';	
			$emailBody = $appendDevMessage . $emailBody;
			$emailSubject = "DEV - " . $emailSubject;
		}
		
		// send out ppv and winner notification emails
		// -------------------------------------------------------------------------------
		$emailHeaders = "From: $emailFrom\r\n";
		$emailHeaders.= "Cc: $emailCc\r\n";
		$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
		$emailHeaders.= "Bcc: $emailBcc\r\n";
    	$emailHeaders.= "Content-type: text/html\r\n";
		
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
		
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
			@mail('devmail@luxurylink.com', 'WEB SERVICE TICKETS: ppv record not saved', print_r($ppvNoticeSave, true));	
		}
		
		// update ticket status if required
		// -------------------------------------------------------------------------------
		$newTicketStatus = false;
		if ($ppvNoticeTypeId == 1) {  
			// reservation confirmation
			$newTicketStatus = 4;	
			$resData = $this->Ticket->query("SELECT * FROM reservation WHERE ticketId = $ticketId");
			if (!empty($resData)) {
				$reservationId = $resData[0]['reservation']['reservationId'];
				$reservation = array();
				$reservation['reservationId'] = $reservationId;
				$reservation['ticketId'] = $ticketId;
				$reservation['reservationConfirmToCustomer'] = date('Y:m:d H:i:s', strtotime('now'));
				$this->Reservation->save($reservation);
			}
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
		
	function findValidUserPaymentSetting($userId, $bidId = null) {
		
		$ups = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc");
		if ($bidId && is_numeric($bidId)) {
			$ups = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userId = $userId and userPaymentSettingId = (select userPaymentSettingId from bid where bidId = $bidId)");
		}
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

		// DEV STOP!
		// ---------------------------------------------------------------------------
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			@mail('devmail@luxurylink.com','DEV - PAYMENT STOP', print_r($data, true));
			die();
		}

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
		
		// handle fees, promo discounts, etc
		// ---------------------------------------------------------------------------
		$fee = $this->Ticket->getFeeByTicket($data['ticketId']);
		$totalChargeAmount = $data['paymentAmount'];
		
		$promoGcCofData = array();
		if (!$toolboxManualCharge) {
			// this is either autocharge or user checkout

			// fee gets set in getPromoGcCofData
			$promoGcCofData		= $this->Ticket->getPromoGcCofData($ticket['Ticket']['ticketId'], $totalChargeAmount);
			$totalChargeAmount  = $promoGcCofData['final_price'];

			// for MasterCard sponsor only - check if card is mc
			// -------------------------------------------------------------------------------
			if (isset($promoGcCofData['Promo']) && $promoGcCofData['Promo']['applied'] && (stristr($promoGcCofData['Promo']['promoCode'], 'LLMCWORLD09'))) {
				$isValidPackagePromo = $this->Ticket->__isValidPackagePromo(1, $ticket['Ticket']['packageId']);
				if (!$isValidPackagePromo || $userPaymentSettingPost['UserPaymentSetting']['ccNumber']{0} != '5') {
					$totalChargeAmount += $promoGcCofData['Promo']['totalAmountOff'];
				}
			}
			
			// used promo or gc or cof that resulted in complete ticket price coverage -- no cc charge needed
			// -------------------------------------------------------------------------------
			if ($promoGcCofData['applied'] && ($promoGcCofData['final_price'] == 0)) {
				return $this->runPostChargeSuccess($ticket, $data, $usingUpsId, $userPaymentSettingPost, $promoGcCofData, $toolboxManualCharge);
			}
		}
		
		// set total charge amount to send to processor
		// ---------------------------------------------------------------------------
		$ticket['Ticket']['billingPrice'] = $totalChargeAmount;
		
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
		$paymentDetail['ppBillingAmount']		= $totalChargeAmount;
		$paymentDetail['autoProcessed']			= $data['autoCharge'];
		$paymentDetail['initials']				= $data['initials'];
		$paymentDetail['ccType']				= $userPaymentSettingPost['UserPaymentSetting']['ccType'];

		$this->PaymentDetail->create();
		if (!$this->PaymentDetail->save($paymentDetail)) {
			@mail('devmail@luxurylink.com', 'WEB SERVICE ERROR: PAYMENT PROCESSED BUT NOT SAVED', print_r($this->PaymentDetail->validationErrors,true)  . print_r($paymentDetail, true));
		}
				
		// return result whether success or denied
		// ---------------------------------------------------------------------------
		if ($processor->ChargeSuccess()) {
			return $this->runPostChargeSuccess($ticket, $data, $usingUpsId, $userPaymentSettingPost, $promoGcCofData, $toolboxManualCharge);
		} else {
			return $processor->GetResponseTxt();
		}
	}

	function runPostChargeSuccess($ticket, $data, $usingUpsId, $userPaymentSettingPost, $promoGcCofData, $toolboxManualCharge) {

		// allocate revenue to loa and tracks
		// ---------------------------------------------------------------------------
		$tracks = $this->TrackDetail->getTrackRecord($ticket['Ticket']['ticketId']);
		if (!empty($tracks)) {
			foreach ($tracks as $track) {

				// decrement loa number of packages
				// ---------------------------------------------------------------------------
				if ($track['expirationCriteriaId'] == 2) {
					$this->Ticket->query('UPDATE loa SET numberPackagesRemaining = numberPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1');
				} elseif ($track['expirationCriteriaId'] == 4) {
					$this->Ticket->query('UPDATE loa SET membershipPackagesRemaining = membershipPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1');
				}
				
				// track detail stuff and allocation
				// ---------------------------------------------------------------------------
				$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $ticket['Ticket']['ticketId']);	
				if (!$trackDetailExists) {
					$new_track_detail = $this->TrackDetail->getNewTrackDetailRecord($track, $ticket['Ticket']['ticketId']);
					if ($new_track_detail) {
						$this->TrackDetail->create();
						$this->TrackDetail->save($new_track_detail);
					}
				}
			}
		}
			
		// if saving new user card information
		// ---------------------------------------------------------------------------
		if ($data['saveUps'] && !$usingUpsId && !empty($userPaymentSettingPost['UserPaymentSetting'])) {
			$this->UserPaymentSetting->create();
			$this->UserPaymentSetting->save($userPaymentSettingPost['UserPaymentSetting']);
		}
		
		// update ticket status to FUNDED
		// ---------------------------------------------------------------------------
		$ticketStatusChange = array();
		$ticketStatusChange['ticketId'] = $ticket['Ticket']['ticketId'];
		$ticketStatusChange['ticketStatusId'] = 5;
		
		// if gift cert or cof, create additional payment detail records
		// ---------------------------------------------------------------------------
		if (!$toolboxManualCharge) {
			if (isset($promoGcCofData['GiftCert']) && $promoGcCofData['GiftCert'] && $promoGcCofData['GiftCert']['applied']) {
				$this->PaymentDetail->saveGiftCert($ticket['Ticket']['ticketId'], $promoGcCofData['GiftCert'], $ticket['Ticket']['userId'], $data['autoCharge'], $data['initials']);
			}
			if (isset($promoGcCofData['Cof']) && $promoGcCofData['Cof'] && $promoGcCofData['Cof']['applied']) {
				$this->PaymentDetail->saveCof($ticket['Ticket']['ticketId'], $promoGcCofData['Cof'], $ticket['Ticket']['userId'], $data['autoCharge'], $data['initials']);
			}
		}
		
		$this->Ticket->save($ticketStatusChange);

		// RAF promo -- send out referrer purchase notification
		// ---------------------------------------------------------------------------
		if (isset($promoGcCofData['Promo']) && $promoGcCofData['Promo']['promoId'] == 60) {
			$ticketReferFriend = $this->TicketReferFriend->read(null, $ticket['Ticket']['ticketId']);
			if (!empty($ticketReferFriend)) {
				$rafData = $this->Promo->getRafData($promoGcCofData['Promo']['promoCodeId']);
				$emailTo = $rafData['User']['email'];
				$emailFrom = $emailReplyTo = 'referafriend@luxurylink.com';
				$emailCc = $emailBcc = '';
				$emailSubject = 'Your Friend Has Made a Luxury Link Purchase';
				$ppvNoticeTypeId = 21;
				$ppvInitials = 'AUTO_RAF';
				$ticketId = $ticket['Ticket']['ticketId'];
				$url = 'http://www.luxurylink.com';
				ob_start();
				include('../vendors/email_msgs/notifications/21_raf_referrer_purchase_notification.html');
				$emailBody = ob_get_clean();
				$this->sendPpvEmail($emailTo, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials);	
			}
		}

		return 'CHARGE_SUCCESS';
	}
}
?>
