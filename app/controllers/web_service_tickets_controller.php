<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';
	var $uses = array('Ticket', 'User', 'Offer', 'Bid', 'ClientLoaPackageRel', 'RevenueModelLoaRel', 'Loa', 'RevenueModelLoaRelDetail', 'PpvNotice');
	var $serviceUrl = 'http://toolboxdev.luxurylink.com/web_service_tickets';
	//var $serviceUrl = 'http://192.168.100.111/web_service_tickets';
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
		$offerTypeToFormat = $this->Offer->query("SELECT formatId FROM formatOfferTypeRel WHERE offerTypeId = " . $offerData['SchedulingInstance']['SchedulingMaster']['offerTypeId']);
		$formatId = $offerTypeToFormat[0]['formatOfferTypeRel']['formatId'];
		
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
	
			// ticket is now created. werd
			$ticketId = $this->Ticket->getLastInsertId();
			
			// update the tracks 
			$this->updateTrackPending($ticketId);
			
			// if non-auction, just stop here as charging and ppv should not be auto
			if ($formatId != 1) {
				return true;	
			}
			
			// find out if there is a valid credit card to charge.  charge and send appropiate emails
			$user_payment_setting = $this->findValidUserPaymentSetting($userData['User']['userId']);
			
			// param settings for the ppv function
			$ppv_settings = array();
			$ppv_settings['ticketId'] 		= $ticketId;
			$ppv_settings['send'] 			= 1;
			$ppv_settings['returnString']	= 0;
			
			if (is_array($user_payment_setting) && !empty($user_payment_setting)) {
				// has valid cc card to charge
				$ppv_settings['ppvNoticeTypeId'] 	= 5;
				$ppv_settings['autoProcessPpv'] = 1;
			} elseif ($user_payment_setting == 'EXPIRED') {
				// has valid cc card but is expired
				$ppv_settings['ppvNoticeTypeId'] 	= 8;
			} else {
				// has no valid cc on file
				$ppv_settings['ppvNoticeTypeId'] 	= 6;
			}
			
			// send out winner notifications
			$this->ppv(json_encode($ppv_settings));
			return true;	
		} else {			
			$this->errorResponse = 908;
			return false;
		}
	}
		
	function ppv($in0) {
		$params = json_decode($in0, true);
		
		// required params for sending and viewing ppvs
		$ticketId 			= $params['ticketId'];
		$send 				= $params['send'];
		$returnString 		= $params['returnString'];
		$ppvNoticeTypeId	= $params['ppvNoticeTypeId'];
		
		// error checking here for required params
		
		$this->Ticket->recursive = 0;
		$ticket = $this->Ticket->read(null, $ticketId);

		$this->ClientLoaPackageRel->recursive = 0;
		$clientLoaPackageRel = $this->ClientLoaPackageRel->findBypackageid($ticket['Ticket']['packageId']);
		
		$liveOffer	= $this->Ticket->query("select * from livedev.offer as LiveOffer where offerId = " . $ticket['Ticket']['offerId'] . " limit 1");
		
		// data arrays
		// ---------------------------------------------------------
		$ticketData 	= $ticket['Ticket'];
		$packageData 	= $ticket['Package'];
		$offerData 		= $ticket['Offer'];
		$userData 		= $ticket['User'];
		$clientData 	= $clientLoaPackageRel['Client'];
		$loaData 		= $clientLoaPackageRel['Loa'];
		$liveOfferData 	= $liveOffer[0]['LiveOffer'];

		// vars for email templates
		// ----------------------------------------------------------
		$userId 			= $userData['userId'];
		$userFirstName 		= ucwords(strtolower($userData['firstName']));
		$userLastName 		= ucwords(strtolower($userData['lastName']));
		$userEmail 			= $userData['email'];
		$offerId			= $offerData['offerId'];
		$clientId			= $clientData['clientId'];
		$oldProductId		= $clientData['oldProductId'];
		$packageName 		= $packageData['packageName'];
		$packageSubtitle	= $packageData['subtitle'];
		$clientName 		= $clientData['name'];
		$packageIncludes 	= $packageData['packageIncludes'];
		$legalText			= $packageData['legalText'];
		$validityNote		= $packageData['validityNote'];
		$offerTypeId		= $offerData['offerTypeId'];
		$offerEndDate		= date('M d Y H:i A', strtotime($liveOfferData['endDate']));
		$billingPrice		= number_format($ticketData['billingPrice'], 2, '.', ',');
		$llFeeAmount		= in_array($offerTypeId, array(1,2,6)) ? 30 : 40;
		$llFee				= number_format($llFeeAmount, 2, '.', ',');
		$totalPrice			= number_format(($ticketData['billingPrice'] + $llFeeAmount),  2, '.', ',');
		$maxNumWinners		= $liveOfferData['maxNumWinners'];
		
		$checkoutHash		= md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
		$checkoutKey		= base64_encode(serialize(array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)));
		$checkoutLink		= "https://www.luxurylink.com/my/my_purchse.php?z=$checkoutKey";
		
		$show_mc 			= false;

		ob_start();
		switch ($ppvNoticeTypeId) {
			case 1:
				include('../vendors/email_msgs/ppv/conf_ppv.html');
				$emailSubject = 'testing conf ppv';
				break;
			case 2:
				include('../vendors/email_msgs/ppv/res_ppv.html');
				$emailSubject = 'testing res ppv';
				break;
			case 3:
				include('../vendors/email_msgs/ppv/winner_ppv.html');
				$emailSubject = 'testing winner ppv';
				break;
			case 4: 
				include('../vendors/email_msgs/ppv/client_ppv.html');
				$emailSubject = 'testing client ppv';
				break;
			case 5:
				include('../vendors/email_msgs/notifications/winner_notification.html');
				$emailSubject = 'testing winn notif';
				break;
			case 6:
				include('../vendors/email_msgs/notifications/winner_notification_w_checkout.html');
				$emailSubject = 'testing winn notif w checkout';
				break;
			case 7:
				include('../vendors/email_msgs/notifications/winner_notification_decline_cc.html');
				$emailSubject = 'testing winn notif w decline cc';
				break;
			case 8:
				include('../vendors/email_msgs/notifications/winner_notification_expired_cc.html');
				$emailSubject = 'testing winn notif w expired cc';
				break;
			default:
				break;
		}
		
		$emailBody = ob_get_clean();
	
		if ($send) {
			$emailTo = 'devmail@luxurylink.com';
			$emailFrom = 'LuxuryLink.com<auction@luxurylink.com>';
			$emailHeaders = "From: $emailFrom\r\n";
        	$emailHeaders.= "Content-type: text/html\r\n";
			
			// send out email now
			@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
			
			$emailSentDatetime = strtotime('now');

			$emailBodyFileName = $ticketId . '_' . $ppvNoticeTypeId . '_' . $emailSentDatetime . '.html';
			
			$fh = fopen("../vendors/email_msgs/toolbox_sent_messages/$emailBodyFileName", 'w');
			fwrite($fh, $emailBody);
			fclose($fh);
			
			$ppvNoticeSave = array();
			$ppvNoticeSave['ppvNoticeTypeId']	= $ppvNoticeTypeId; 
			$ppvNoticeSave['ticketId'] 			= $ticketId;
			$ppvNoticeSave['emailTo']			= $emailTo;
			$ppvNoticeSave['emailFrom']			= $emailFrom;
			$ppvNoticeSave['emailCc']			= '';
			$ppvNoticeSave['emailSubject']		= $emailSubject;
			$ppvNoticeSave['emailBodyFileName']	= $emailBodyFileName;
			$ppvNoticeSave['emailSentDatetime']	= $emailSentDatetime;
			
			@mail('alee@luxurylink.com', 'testing', print_r($ppvNoticeSave, true), $emailHeaders);
			
			$this->PpvNotice->create();
			$this->PpvNotice->save($ppvNoticeSave);
		}
		
		if ($returnString) {
			return $emailBody;	
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
		} else {
				
		}
	}
	
	function updateTrackDetail($in0) {
		$data = json_decode($in0, true);
		
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		
		$this->ClientLoaPackageRel->recursive = -1;
		$clientLoaPackageRel = $this->ClientLoaPackageRel->findBypackageid($data['packageId']);	
	
		$ticketId = $ticket['Ticket']['ticketId'];
		$loaId = $clientLoaPackageRel['ClientLoaPackageRel']['loaId'];
		
		$this->Loa->recursive = -1;
		$loa = $this->Loa->read(null, $loaId);
		
		// ---------------- rev stuff ---------------------
		
		$this->RevenueModelLoaRel->recursive = 2;
		$revenueModelLoaRel = $this->RevenueModelLoaRel->findByloaid($loaId);
	
		$exp 			= $revenueModelLoaRel['ExpirationCriterium'];
		$model 			= $revenueModelLoaRel['RevenueModel'];
		$track 			= $revenueModelLoaRel['RevenueModelLoaRel'];
		$trackDetail 	= $revenueModelLoaRel['RevenueModelLoaRelDetail'];
				
		$revModelLoaDetailSave							= array();
		$revModelLoaDetailSave['revenueModelLoaRelId'] 	= $track['revenueModelLoaId'];
		$revModelLoaDetailSave['ticketId'] 				= $ticketId;
		
		switch ($model['revenueModelId']) {
			case 1:
				if (empty($trackDetail)) {
					$revModelLoaDetailSave['cycle'] 				= 1;
					$revModelLoaDetailSave['iteration'] 			= 1;
				} else {
					$revModelLoaDetailSave['cycle'] 				= $trackDetail['cycle'];
					$revModelLoaDetailSave['iteration'] 			= $trackDetail['iteration']++;
				}
				$revModelLoaDetailSave['amountKept'] 			= ($track['keepPercentage'] / 100) * $ticket['Ticket']['billingPrice'];
				$revModelLoaDetailSave['amountRemitted'] 		= $ticket['Ticket']['billingPrice'] - $revModelLoaDetailSave['amountKept'];
				
				break;
			case 2:
				break;
			case 3:
				break;
		}
		return print_r($revModelLoaDetailSave, true);
		
		// ------------- end rev stuff --------------------
	}
	
}
?>