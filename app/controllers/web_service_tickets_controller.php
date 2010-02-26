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
					  'Address', 'OfferLuxuryLink', 'SchedulingMaster', 'SchedulingInstance', 'Reservation',
					  'PromoTicketRel', 'Promo', 'TicketReferFriend'
					  );

	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_tickets';
	var $serviceUrlDev = 'http://alee-toolboxdev.luxurylink.com/web_service_tickets';
	var $serviceUrlStage = 'http://stage-toolbox.luxurylink.com/web_service_tickets';
	var $errorResponse = false;
	var $errorMsg = false;
	var $errorTitle = false;
	var $api = array(
					'processNewTicket' => array(
						'doc' => 'ticket processor functionality for family and luxurylink',
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
						)
					);
					
	function beforeFilter() { $this->LdapAuth->allow('*'); }
	
	function processNewTicket($in0) {
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = $this->errorMsg = $this->errorTitle = false;
		if (!$this->processTicket($json_decoded)) {			
			$server_type = '';
			if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
				$server_type = '[DEV] --> ';
			}
			if (stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$server_type = '[STAGE] --> ';
			} 
			@mail('devmail@luxurylink.com', "$server_type" . 'WEBSERVICE (TICKETS): ERROR ('. $this->errorResponse . ')' . $this->errorTitle , $this->errorMsg . "<br /><br />\n\n" . print_r($json_decoded, true));
			return 'FAIL';
		}  else {
			return 'SUCCESS';
		}
	}

	function processFixedPriceTicket($ticketData) {
		if (!$ticketData['ticketId']) {
			return false;
		}

		// send out fixed price request emails
		// -------------------------------------------------------------------------------
		$params 					= array();
		$params['ticketId']			= $ticketData['ticketId'];
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'AUTO';
		
		// send out fixed price emails
		// -------------------------------------------------------------------------------
		$params['ppvNoticeTypeId'] = 9;     // Fixed Price - Winner Notification
		$this->ppv(json_encode($params));	
		
		if (trim($ticketData['requestNotes'])) {
			$params['ppvNoticeTypeId'] = 10;     // Fixed Price - Client Exclusive Email
		} else {
			$params['ppvNoticeTypeId'] = 2;     // Reservation Request
		}
		$this->ppv(json_encode($params));	
		
		$params['ppvNoticeTypeId'] = 11;     // Fixed Price - Internal Exclusive Email
		$this->ppv(json_encode($params));
		
		// return ticket id to the frontend live site
		// -------------------------------------------------------------------------------
		return true;
	}

	function processTicket($data) {
		// if we do not have these values then void
		// -------------------------------------------------------------------------------
		if (empty($data) || !is_array($data)) {
			$this->errorResponse = 1101;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted due to receiving invalid data.';
			return false;	
		}
		if (!isset($data['ticketId']) || empty($data['ticketId'])) {
			$this->errorResponse = 1102;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted because the required field ticketId was not supplied.';
			return false;	
		}
		if (!isset($data['userId']) || empty($data['userId'])) {
			$this->errorResponse = 1103;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted because the required field userId was not supplied.';
			return false;	
		}
		if (!isset($data['offerId']) || empty($data['offerId'])) {
			$this->errorResponse = 1104;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted because the required field offerId was not supplied.';
			return false;	
		}
		
		$this->Offer->recursive = 2;
		$offerData = $this->Offer->read(null, $data['offerId']);
		$clientData = $this->Ticket->getClientsFromPackageId($data['packageId']);
		
		// gather all data that is necessary
		// -------------------------------------------------------------------------------
		switch ($data['siteId']) {
			case 1:
				$ticketSite = 'offerLuxuryLink';
				break;
			case 2:
				$ticketSite = 'offerFamily';
				break;
		}
		if ($ticketSite) {
			$offerLive = $this->Offer->query("SELECT * FROM $ticketSite WHERE offerId = " . $data['offerId']);
			$offerLive = $offerLive[0][$ticketSite];	
		}

		// lets do some checking! in case of dup tickets or if ticket was already processed
		// -------------------------------------------------------------------------------
		$ticket_toolbox = $this->Ticket->read(null, $data['ticketId']);
		if (empty($ticket_toolbox) || !$ticket_toolbox) {
			$this->errorResponse = 187;
			$this->errorTitle = 'Ticket Not Processsed [CHECK REPLICATION]';
			$this->errorMsg = 'Ticket data not replicated to Toolbox yet or TB DB is down.  This ticket has been flagged for reprocessing and will finish the process when the systems come online.';
			return false;
		}

		$ticket_payment = $this->PaymentDetail->query('SELECT * FROM paymentDetail WHERE ticketId = ' . $data['ticketId']);
		if (!empty($ticket_payment)) {
			$this->errorResponse = 188;
			$this->errorTitle = 'Payment Already Detected for Ticket';
			$this->errorMsg = 'Stopped processing this ticket.  An existing payment has been detected for this ticket id whether it was successful or not.  This ticket has been marked as processed successfully.';
			return true;
		}

		// all ticket processing happens in here
		// -------------------------------------------------------------------------------
		if ($ticket_toolbox['Ticket']['transmitted'] == 0) {

			$ticketId = $data['ticketId'];

			// update the tracks
			// -------------------------------------------------------------------------------
			$schedulingMasterId = $offerData['SchedulingInstance']['SchedulingMaster']['schedulingMasterId'];
			$smid = $this->Track->query("SELECT trackId FROM schedulingMasterTrackRel WHERE schedulingMasterId = $schedulingMasterId LIMIT 1");
			$smid = $smid[0]['schedulingMasterTrackRel']['trackId'];
			if (!empty($smid)) {
				$this->addTrackPending($smid, $data['billingPrice']);
			}
			
			// take down future instances of offers if reached package.maxNumSales
			// -------------------------------------------------------------------------------
			$this->Ticket->__runTakeDownPackageNumPackages($data['packageId'], $ticketId);

			$expirationCriteriaId = $this->Ticket->getExpirationCriteria($ticketId);
			switch ($expirationCriteriaId) {
				case 1:
					$this->Ticket->__runTakeDownLoaMemBal($data['packageId'], $ticketId, $data['billingPrice']);
					break;
				case 4:
					$this->Ticket->__runTakeDownLoaNumPackages($data['packageId'], $ticketId);
					break;
				case 5:
					$this->Ticket->__runTakeDownRetailValue($offerLive['clientId'], $offerLive['retailValue'], $ticketId);
					break;
			}

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
			if (!in_array($data['offerTypeId'], array(1,2,6))) {
				$this->processFixedPriceTicket($data);
				return true;
			}
			
			// find out if there is a valid credit card to charge.  charge and send appropiate emails
			// -------------------------------------------------------------------------------
			$user_payment_setting = $this->findValidUserPaymentSetting($data['userId'], $data['userPaymentSettingId']);
			
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
			if ($offerLive['isMystery'] || $offerLive['retailValue'] == 1 || $offerLive['openingBid'] == 1) {
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
				return true;
			}
		
			if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$auto_charge_card = false;
			}

			$autoSendClientWinnerPpv = false;
			// auto charge here
			// -------------------------------------------------------------------------------
			if (!$restricted_auction && $auto_charge_card) {
				$data_post = array();
		        $data_post['userId']                 = $data['userId'];
		        $data_post['ticketId']               = $ticketId;
		        $data_post['paymentProcessorId']     = 1;
				if ($data['siteId'] == 2) {
		        	$data_post['paymentProcessorId']     = 3; // FAMILY site uses PAYPAL
				}
		        $data_post['paymentAmount']          = $data['billingPrice'];
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
			$this->errorResponse = 1105;
			$this->errorMsg = "Detected re-processing of ticket.";
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

	function numF($str) {
		// for commas thousand group separater
		return number_format($str);
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
		switch ($ticket['Ticket']['siteId']) {
			case 1:
				$offerSite = 'offerLuxuryLink';
				break;
			case 2:
				$offerSite = 'offerFamily';
				break;
		}
		$liveOffer	= $this->Ticket->query("select * from $offerSite as LiveOffer where offerId = " . $ticket['Ticket']['offerId'] . " limit 1");
	
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
		
		// ***************************************************************************************************************
		// ALL VARIABLES ARE SET HERE -- THIS WAY WE DONT HAVE TO CHANGE A MILLION TEMPLATES IF CHANGE IS MADE TO DB FIELD
		// ***************************************************************************************************************

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

		$billingPrice		= $this->numF($ticketData['billingPrice']);
		$llFeeAmount		= 40;
		$llFee				= $llFeeAmount;
		$totalPrice			= $this->numF($ticketData['billingPrice'] + $llFeeAmount);
		$maxNumWinners		= $liveOfferData['numWinners'];
		
		$checkoutHash		= md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
		$checkoutKey		= base64_encode(serialize(array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)));

		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$checkoutLink		= "https://alee-lldev.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
		} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
			$checkoutLink		= "https://stage-luxurylink.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
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
				$guarantee = $this->numF($packageData['reservePrice']);
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
		$clientAdjustedPrice = $this->numF(($clients[$client_index]['percentOfRevenue'] / 100) * $ticketData['billingPrice']);
		
		// ********* SITE NAME **********
		switch ($ticketData['siteId']) {
			case 1:
				$siteName = 'Luxury Link';
				$siteDisplay = 'LuxuryLink.com';
				$siteEmail = 'luxurylink.com';
				$siteUrl = 'http://www.luxurylink.com/';
				$siteHeader = '990000';
				$sitePhone  = '(888) 297-3299';
				$sitePhoneLocal = '(310) 215-8060';
				$siteFax = '(310) 215-8279';
				$headerLogo = 'http://www.luxurylink.com/images/ll_logo_2009_2.gif';
				$checkoutLink		= "https://www.luxurylink.com/my/my_purchase.php?z=$checkoutKey";

				// auction facilitator
				// -------------------------------------------------------------------------------
				$dateRequestLink = "https://www.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
					$dateRequestLink = "https://alee-lldev.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
					$dateRequestLink = "https://stage-luxurylink.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				}

				break;
			case 2:
				$siteName = 'Family Getaway';
				$siteDisplay = 'FamilyGetaway.com';
				$siteEmail = 'familygetaway.com';
				$siteUrl = 'http://www.familygetaway.com/';
				$siteHeader = 'DE6F0A';
				$sitePhone  = '(877) 372-5877';
				$sitePhoneLocal = '(310) 956-3703';
				$siteFax = '(800) 440-3820';
				$headerLogo = 'http://www.luxurylink.com/images/family/logo_emails.gif';
				$checkoutLink		= "https://www.familygetaway.com/my/my_purchase.php?z=$checkoutKey";

				// auction facilitator
				// -------------------------------------------------------------------------------
				$dateRequestLink = "https://www.familygetaway.com/my/my_date_request.php?tid=$ticketId";
				if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
					$dateRequestLink = "https://alee-familydev.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
					$dateRequestLink = "https://stage-family.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				}

				break;
		}
		$siteId = $ticketData['siteId'];

		// fetch template with the vars above
		// -------------------------------------------------------------------------------
		ob_start();
		switch ($ppvNoticeTypeId) {
			case 1:
				// send out res confirmation
				include('../vendors/email_msgs/ppv/conf_ppv.html');
				$emailSubject = "Your $siteName Booking is Confirmed - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<resconfirm@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resconfirm@$siteEmail" : "reservations@$siteEmail";
				break;
			case 2:
				// send out res request
				include('../vendors/email_msgs/ppv/res_ppv.html');
				$emailSubject = "Please Confirm This $siteName Booking - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 4: 
				include('../vendors/email_msgs/ppv/client_ppv.html');
				$emailSubject = "$siteName Auction Winner Notification - $userFirstName $userLastName";
				$emailFrom = "$siteDisplay<auctions@$siteEmail>";
				$emailReplyTo = "auctions@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 5:
				include('../vendors/email_msgs/notifications/winner_notification.html');
				$emailSubject = "$siteName Auction Winner - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 9:
				include('../vendors/email_msgs/fixed_price/msg_fixedprice.html');
				$emailSubject = "$siteName - Your Request Has Been Received";
				$emailFrom = "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = "exclusives@$siteEmail";
				break;
			case 10:
				include('../vendors/email_msgs/fixed_price/msg_client_fixedprice.html');
				$emailSubject = "An Exclusive $siteName Booking Request Has Come In!";
				$emailFrom = "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = "exclusives@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 11:
				include('../vendors/email_msgs/fixed_price/msg_internal_fixedprice.html');
				$emailSubject = "A $siteName $fpRequestType Request Has Come In!";
				$emailFrom = "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = "exclusives@$siteEmail";
				$userEmail = "exclusives@$siteEmail";
				break;
			case 12:
				include('../vendors/email_msgs/fixed_price/notification_acknowledgement.html');
				$emailSubject = "Your $siteName Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<auction@$siteEmail>" : "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
				break;
			case 13:
				include('../vendors/email_msgs/fixed_price/notification_dates_available.html');
				$emailSubject = "Your $siteName Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<auction@$siteEmail>" : "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
				break;
			case 14:
				include('../vendors/email_msgs/fixed_price/notification_dates_not_available.html');
				$emailSubject = "Your $siteName Travel Booking - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<auction@$siteEmail>" : "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
				break;
			case 15:
				include('../vendors/email_msgs/notifications/chase_money_notification.html');
				$emailSubject = "$siteName Auction Winner - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 16:
				include('../vendors/email_msgs/notifications/first_offense_flake.html');
				$emailSubject = "$siteName Auction Winner - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 17:
				include('../vendors/email_msgs/notifications/second_offense_flake.html');
				$emailSubject = "$siteName Auction Winner - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 18:
				include('../vendors/email_msgs/notifications/18_auction_winner_ppv.html');
				$emailSubject = "$siteName Auction Winner Receipt - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 19:
				include('../vendors/email_msgs/notifications/19_auction_winner_declined_expired.html');
				$emailSubject = "$siteName Auction Winner Notification - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 20:
				include('../vendors/email_msgs/notifications/20_auction_your_dates_received.html');
				$emailSubject = "Your $siteName Request has been Received - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<auction@$siteEmail>" : "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
				break;
			default:
				break;
		}
		$emailBody = ob_get_clean();

		if (in_array($ppvNoticeTypeId, array(5,18,19)) && $liveOfferData['isMystery']) {
			$emailSubject = "$siteName Mystery Auction Winner";
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
					$clientAdjustedPrice = $this->numF(($clients[$i]['percentOfRevenue'] / 100) * $ticketData['billingPrice']);
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
					$this->sendPpvEmail($clientPrimaryEmail, $emailFrom, $clientCcEmail, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials);	
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
		
		if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
			$appendDevMessage = "---- DEV MAIL ---- \n<br />ORIGINAL TO:  $emailTo\n<br />ORIGINAL CC: $emailCc\n<br />ORIGINAL BCC: $emailBcc";
			$emailTo = $emailCc = $emailBcc = 'devmail@luxurylink.com';	
			$emailBody = $appendDevMessage . $emailBody;
			$emailBody.= print_r($_SERVER, true);
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
		
	function findValidUserPaymentSetting($userId, $upsId = null) {
		if ($upsId && is_numeric($upsId)) {
			$ups = $this->User->query("SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE userId = $userId AND userPaymentSettingId = $upsId");
		} else {
			$ups = $this->User->query("SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE userId = $userId AND inactive = 0 ORDER BY primaryCC DESC, expYear DESC");
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
		if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
			$data[] = $_SERVER;
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
		
		// for FAMILY, payment is via PAYPAL only [override]
		// ---------------------------------------------------------------------------
		if ($ticket['Ticket']['siteId'] == 2) {
			$data['paymentProcessorId'] = 3;
		}

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
		$promoGcCofData		= $this->Ticket->getPromoGcCofData($ticket['Ticket']['ticketId'], $totalChargeAmount);
		if (!$toolboxManualCharge) {
			// this is either autocharge or user checkout

			// fee gets set in getPromoGcCofData
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
						if (!$this->TrackDetail->save($new_track_detail)) {
							mail('devmail@luxurylink.com', $ticket['Ticket']['ticketId'] . ' ticket track detail not saved', print_r($ticket, true));
						}
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
		
		// ********* SITE NAME **********
		switch ($ticket['Ticket']['siteId']) {
			case 1:
				$siteName = 'Luxury Link';
				$url = 'http://www.luxurylink.com';
				$emailFrom = $emailReplyTo = 'referafriend@luxurylink.com';
				break;
			case 2:
				$siteName = 'Family';
				$url = 'http://www.familygetaway.com';
				$emailFrom = $emailReplyTo = 'referafriend@familygetaway.com';
				break;
			default:
				$siteName = '';
		}

		// RAF promo -- send out referrer purchase notification
		// ---------------------------------------------------------------------------
		if (isset($promoGcCofData['Promo']) && $promoGcCofData['Promo']['promoId'] == 60) {
			$ticketReferFriend = $this->TicketReferFriend->read(null, $ticket['Ticket']['ticketId']);
			if (!empty($ticketReferFriend)) {
				$rafData = $this->Promo->getRafData($promoGcCofData['Promo']['promoCodeId']);
				$emailTo = $rafData['User']['email'];
				$emailCc = $emailBcc = '';
				$emailSubject = "Your Friend Has Made a $siteName Purchase";
				$ppvNoticeTypeId = 21;
				$ppvInitials = 'AUTO_RAF';
				$ticketId = $ticket['Ticket']['ticketId'];
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
