<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');
App::import('Vendor', 'aes.php');
require(APP.'/vendors/pp/Processor.class.php');

// FOR DEV WEB SERVICE SETTINGS! VERY IMPORTANT FOR DEV
define('DEV_USER_TOOLBOX_HOST', 'http://' . $_SERVER['ENV_USER'] . '-toolboxdev.luxurylink.com/web_service_tickets');
define('DEV_USER', $_SERVER['ENV_USER']);

class WebServiceTicketsController extends WebServicesController
{
	var $name = 'WebServiceTickets';

	var $uses = array('Ticket', 'UserPaymentSetting','PaymentDetail', 'Client', 'User', 'Offer', 'Bid',
					  'ClientLoaPackageRel', 'Track', 'OfferType', 'Loa', 'TrackDetail', 'PpvNotice',
					  'Address', 'OfferLuxuryLink', 'SchedulingMaster', 'SchedulingInstance', 'Reservation',
					  'PromoTicketRel', 'Promo', 'TicketReferFriend','Package','PaymentProcessor'
					  );

	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_tickets';
	var $serviceUrlStage = 'http://stage-toolbox.luxurylink.com/web_service_tickets';

	// IF DEV, please make sure you use this path, or if using your own dev, then change this var
	var $serviceUrlDev = DEV_USER_TOOLBOX_HOST;

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
					'sendResRequestReminder' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'sendResRequestReminderCustomer' => array(
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
					'getPromoGcCofData' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'processPaymentTicket' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetDatesNotAvail' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetDatesConfirmed' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetDatesConfirmedSeasonalPricing' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'FixedPriceCardCharge' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetDateResRequested' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetDatesConfirmedOnlyProperty' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetCCDeclined' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetCancelConfirmation' => array(
						'doc' => 'N/A',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'autoSendXnetResCancelled' => array(
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

	function getPromoGcCofData($in0) {
		$data = json_decode($in0, true);
		if (!empty($data) && isset($data['ticketId']) && isset($data['billingPrice'])) {
			return json_encode($this->Ticket->getPromoGcCofData($data['ticketId'], $data['billingPrice']));
		}
		return '0';
	}

	function processFixedPriceTicket($ticketData) {
		if (!$ticketData['ticketId']) {
			$this->errorResponse = 2001;
			$this->errorTitle = 'Missing Ticket ID';
			$this->errorMsg = 'Fixed Price Ticket processing was aborted due to receiving invalid data.';
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

		//special request
		if (trim($ticketData['requestNotes'])) {
			$params['ppvNoticeTypeId'] = 10;     // Fixed Price - Client Exclusive Email
		} else {
			#$params['ppvNoticeTypeId'] = 25;     // Reservation Request w/ no xnet (to be removed)
			$params['ppvNoticeTypeId'] = 2;     // Reservation Request new one with xnet
		}

		// check reservation checkin date - if 48 hrs send ppvid 10
		$arrival_date_1 = $ticketData['requestArrival'] && $ticketData['requestArrival'] != '0000-00-00' ? @strtotime($ticketData['requestArrival']) : false;
		$arrival_date_2 = $ticketData['requestArrival2'] && $ticketData['requestArrival2'] != '0000-00-00' ? @strtotime($ticketData['requestArrival2']) : false;

		$arrival_within_2_days = strtotime('+2 DAYS');     // 48 hrs from now
		if ($arrival_date_1 > 0 && $arrival_date_1 <= $arrival_within_2_days) {
			$params['ppvNoticeTypeId'] = 10;
		}
		if ($arrival_date_2 > 0 && $arrival_date_2 <= $arrival_within_2_days) {
			$params['ppvNoticeTypeId'] = 10;
		}
		//if multi-product offer, then send old res request w/o client res xtranet
		if ($this->Ticket->isMultiProductPackage($params['ticketId'])) {
			$params['ppvNoticeTypeId'] = 10;    // old res request
		}
		$expirationCriteriaId = $this->Ticket->getExpirationCriteria($params['ticketId']);
		if ($expirationCriteriaId == 5) {
			// this is retail value
			$params['ppvNoticeTypeId'] = 10;    // old res request
		}
		//if request comes in for more than the package NumNights, same as special request
        //acarney 2011-01-18 -- disabling the following block of code because we do not allow
        //users to enter their own departure dates anymore

		$package = $this->Package->read(null, $ticketData['packageId']);
        if (0) {
            $interval1 = (strtotime($ticketData['requestDeparture']) - strtotime($ticketData['requestArrival'])) / 86400;
            if($interval1 > $package['Package']['numNights'] ){
                $params['ppvNoticeTypeId'] = 10;    // old res request
            }

            if($ticketData['requestArrival2'] && $ticketData['requestArrival2'] != '000-00-00') {
                $interval2 = (strtotime($ticketData['requestDeparture2']) - strtotime($ticketData['requestArrival2'])) / 86400;
                if($interval2 > $package['Package']['numNights'] ){
                    $params['ppvNoticeTypeId'] = 10;    // old res request
                }
            }
        }

		$this->ppv(json_encode($params));

		$params['ppvNoticeTypeId'] = 11;     // Fixed Price - Internal Exclusive Email
		$this->ppv(json_encode($params));

		// return ticket id to the frontend live site
		// -------------------------------------------------------------------------------
		return true;
	}

	function processTicket($data) {

		// TODO optimize and implement error handler class

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
		} else {
			$this->errorTitle = 'Invalid Site';
			$this->errorMsg = 'Ticket processing was aborted because site was not supplied.';
			return false;
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

		// if record exists in paymentDetail, this ticket got processed already.
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


			// 2011-05-04 jwoods - fill in ticket.guaranteeAmt if necessary
			// -------------------------------------------------------------------------------
			$guaranteeAmount = 0;
			$sql = "SELECT *
					FROM offer o
					INNER JOIN schedulingInstance i ON o.schedulingInstanceId = i.schedulingInstanceId
					INNER JOIN schedulingMaster m ON i.schedulingMasterId = m.schedulingMasterId
					WHERE o.offerId = ?
					AND m.offerTypeId IN (3,4)
					AND m.isDiscountedOffer = 1
					AND m.percentDiscount > 0 LIMIT 1";
			$resultsDiscount = $this->Ticket->query($sql, array($offerLive['offerId']));

			// was original buy now price discounted?
			if (!empty($resultsDiscount)) {
				$guaranteeAmount = round($data['billingPrice'] / ((100 - $resultsDiscount[0]['m']['percentDiscount']) / 100));

			// else, was a reserve amount set?
			} elseif (intval($offerLive['reserveAmt']) > 0) {
                $guaranteeAmount = $offerLive['reserveAmt'];
			}

            if ($guaranteeAmount > 0) {
                $this->Ticket->query("UPDATE ticket SET guaranteeAmt = ? WHERE ticketId = ?", array($guaranteeAmount, $ticketId));
            }




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
			if ($this->Ticket->__runTakeDownPricePointNumPackages($offerLive['pricePointId'], $ticketId)) {
				$this->Ticket->__runTakeDownLoaMemBal($data['packageId'], $ticketId, $data['billingPrice']);
				$this->Ticket->__runTakeDownLoaNumPackages($data['packageId'], $ticketId);
			}

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

			// find and set promos for this new ticket + refer friend relationship setup
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
				return $this->processFixedPriceTicket($data);
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

			// 07/20/11 jwoods - ticket 581 - treat Mystery like other auctions
			// if ($offerLive['isMystery'] || $offerLive['retailValue'] == 1 || $offerLive['openingBid'] == 1) {
			// 	$restricted_auction = true;
			// }
            if (stristr($offerLive['offerName'], 'RED') && stristr($offerLive['offerName'],'HOT')) {
            	$restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'FEATURED') && stristr($offerLive['offerName'],'AUCTION')) {
            	$restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'AUCTION') && stristr($offerLive['offerName'],'DAY')) {
            	$restricted_auction = true;
            }

			// hack june 29 2010
			if ($clientData[0]['Client']['clientId'] == 378) {
            	$restricted_auction = false;
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

		$aucPreferDates = $params['dates_json'];
		unset($params['dates_json']);

		// send both the my dates have been received and reservation request
		// -------------------------------------------------------------------------------
		$params['ppvNoticeTypeId'] = 20;     // Your Dates Have Been Received
		$this->ppv(json_encode($params));

		// ppvNoticeTypeId 2 is the new res request with client res xtranet
		$params['ppvNoticeTypeId'] = 2;    // Reservation Request

		// if multi-product offer, then send old res request w/o client res xtranet
		if ($this->Ticket->isMultiProductPackage($params['ticketId'])) {
			$params['ppvNoticeTypeId'] = 10;    // old res request
		}
		$expirationCriteriaId = $this->Ticket->getExpirationCriteria($params['ticketId']);
		if ($expirationCriteriaId == 5) {
			// this is retail value
			$params['ppvNoticeTypeId'] = 10;    // old res request
		}

		// check if preferred dates are two days - if so send availabilty request only
        if (!empty($aucPreferDates)) {
			$arrival_within_2_days = strtotime('+2 DAYS');     // 48 hrs from now
			foreach ($aucPreferDates as $aucPreferDateRow) {
				$arrival_ts = strtotime($aucPreferDateRow['arrivalDate']);
                //added if auction/ppv is not 2 and less than 48 hours - ticket 1315 toolbox
   	            //if ($arrival_ts > 0 && $arrival_ts <= $arrival_within_2_days) {
				if ($arrival_ts > 0 && $arrival_ts <= $arrival_within_2_days && $params['ppvNoticeTypeId'] != 2) {
					$params['ppvNoticeTypeId'] = 10;
				}
			}
		}

		$this->ppv(json_encode($params));
	}

	function autoSendXnetDatesNotAvail($in0) {
		// from the XNET - dates are NOT available
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_DATES_NOT_AVAIL';
		$params['ppvNoticeTypeId'] = 14;
		$this->ppv(json_encode($params));
	}

	function sendResRequestReminder($in0) {
		// from the XNET - dates are NOT available
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_RES_REMINDER';
		$params['ppvNoticeTypeId'] = 24;
		$this->ppv(json_encode($params));
	}

	function sendResRequestReminderCustomer($in0) {
		// from the XNET - dates are NOT available
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_RES_REMIND_CUST';
		$params['ppvNoticeTypeId'] = 32;
		$this->ppv(json_encode($params));
	}

	function autoSendXnetDatesConfirmed ($in0) {
		// from the XNET - dates are CONFIRMED
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_DATES_CONFIRMED';
		$params['ppvNoticeTypeId'] = 1;
		$this->ppv(json_encode($params));
		$params['ppvNoticeTypeId'] = 23;
		$this->ppv(json_encode($params));
	}
	function autoSendXnetDatesConfirmedOnlyProperty ($in0) {
		// from the XNET - dates are CONFIRMED
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_DATES_CONF_PROP';
		$params['ppvNoticeTypeId'] = 23;
		$this->ppv(json_encode($params));
	}

	function autoSendXnetDatesConfirmedSeasonalPricing ($in0) {
		// from the XNET - dates are CONFIRMED
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_DATES_CONFIRMED';
		$params['ppvNoticeTypeId'] = 1;
		$ticketId = $params['ticketId'];
		$ticket = $this->Ticket->read(null, $ticketId);
		$ticketData 		= $ticket['Ticket'];
		switch ($ticketData['siteId'])
		{
			case 1:
				$siteName = "luxurylink.com";
				break;
			case 2:
				$siteName = "familygetaway.com";
				break;
		}
		$params['override_email_to'] = 'reservations@'.$siteName;
		$this->ppv(json_encode($params));
		$newTicketStatus = 14; //seasonal pricing
		$this->updateTicketStatus($ticketId, $newTicketStatus);

	}

	function FixedPriceCardCharge($in0) {
		$params = json_decode($in0, true);

		//check if valid ticket
		if (empty($params['ticketId'])) {
			$this->errorResponse = 2012;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted due to receiving invalid data.';
			return false;
		}
		$ticketId = $params['ticketId'];
		$ticketData = $this->Ticket->query("SELECT * FROM ticket WHERE ticketId = $ticketId LIMIT 1");

		if(!$ticketData) {
			$this->errorResponse = 2013;
			$this->errorTitle = 'Invalid Data';
			$this->errorMsg = 'Ticket processing was aborted due to receiving invalid ticket data.';
			return false;
		}

		$isChargeSuccess = $this->CardCharge($ticketId);

		if(!$isChargeSuccess && $this->errorResponse) //critical error
			return false;
		else if (!$isChargeSuccess && !$this->errorResponse) { //charge declined
			$newTicketStatus = 15;
			$this->updateTicketStatus($ticketId, $newTicketStatus);

			$paramEncoded = json_encode($params);
			$this->autoSendXnetCCDeclined($paramEncoded);
		}
		else {
			//successfully charged
			$paramEncoded = json_encode($params);
			$this->autoSendXnetDatesConfirmed($paramEncoded);
		}

	}

	function autoSendXnetCCDeclined($in0) {
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_CC_DECLINED';
		$params['ppvNoticeTypeId'] = 19;
		$this->ppv(json_encode($params));

	}

	function CardCharge($ticketId) {

		$ticketData = $this->Ticket->query("SELECT * FROM ticket WHERE ticketId = $ticketId LIMIT 1");
		$ticketData = $ticketData[0]['ticket'];
		$userId = $ticketData['userId'];

		// if valid successful charge exists, then return true
		// =====================================================================
		$checkExists = $this->PaymentDetail->query("SELECT * FROM paymentDetail WHERE ticketId = $ticketId AND userId = $userId");
		if (isset($checkExists[0]['paymentDetail']) && !empty($checkExists[0]['paymentDetail'])) {
			return true;
		}

		// ============================================================
		// ======== [ start process post ] ============================
		// ============================================================


		$gUserPaymentSettingId = $ticketData['userPaymentSettingId'];

		if ($gUserPaymentSettingId) {
			$data = array();
			$data['userId'] 				= $userId;
			$data['ticketId'] 				= $ticketId;
			$data['paymentProcessorId']		= 1;
			if ($ticketData['siteId'] == 2) {
				// for family, use PAYPAL processor
				$data['paymentProcessorId']		= 3;
			}
			$data['paymentAmount']			= $ticketData['billingPrice'];
			$data['initials']				= 'FPCARDCHARGE';
			$data['autoCharge']				= 1; //if system charge set 1
			$data['saveUps']				= 0;
			$data['zAuthHashKey']			= md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);
			$data['userPaymentSettingId']     = $ticketData['userPaymentSettingId'];

			$data_json_encoded = json_encode($data);
			$response = $this->processPaymentTicket($data_json_encoded);

			if (trim($response) == 'CHARGE_SUCCESS') {
				return true;
			} else {
				return false;
			}

		}
		else {
				$this->errorResponse = 2014;
				$this->errorTitle = 'Invalid PaymentSetting Id';
				$this->errorMsg = 'Ticket does not contain the paymentSettingId';
				return false;
		}



	}

	function autoSendXnetDateResRequested($in0) {
		// from the XNET - dates are requested
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_DATES_REQUESTED';
		$params['ppvNoticeTypeId'] = 2;
		$this->ppv(json_encode($params));
	}

	function autoSendXnetCancelConfirmation($in0) {
		// from the XNET - cancellation confirmation - confirmed
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_CANCEL_CONFIRM';
		$params['ppvNoticeTypeId'] = 30;
		$this->ppv(json_encode($params));
	}

	function autoSendXnetResCancelled($in0) {
		// from the XNET - client receipt for confirmed cancellation
		// -------------------------------------------------------------------------------
		$params = json_decode($in0, true);
		$params['send'] 			= 1;
		$params['returnString']		= 0;
		$params['manualEmailBody']	= 0;
		$params['initials']			= 'XNET_CANCEL_RECIEPT';
		$params['ppvNoticeTypeId'] = 31;
		$this->ppv(json_encode($params));
	}

	function numF($str) {
		// for commas thousand group separater
		return number_format($str);
	}

	function ppv($in0) {
		$params = json_decode($in0, true);

		// TODO THIS METHOD NEEDS SOME MAJOR REVAMP

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
		$override_email_subject  = isset($params['override_email_subject']) && !empty($params['override_email_subject']) ? $params['override_email_subject'] : false;

        //added hb for attachment
        // -------------------------------------------------------------------------------
        $email_attachment = isset($params['emailAttachment']) && !empty($params['emailAttachment']) ? $params['emailAttachment'] : false;
        $email_attachment_type = isset($params['emailAttachmentType']) && !empty($params['emailAttachmentType']) ? $params['emailAttachmentType'] : false;

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

		// ********************************************************************************************************
		// ALL VARIABLES ARE SET HERE -- WE DONT HAVE TO CHANGE A MILLION TEMPLATES IF CHANGE IS MADE TO DB FIELD
		// *********************************************************************************************************

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
		//$validityLeadIn     = $packageData['validityLeadInLine'];
		$addtlDescription   = $liveOfferData['additionalDescription'];

		//2011-01-10 mbyrnes
		$numGuests			= $liveOfferData['numGuests'];
		$roomGrade			= $liveOfferData['roomGrade'];
		$packageBlurb		= ucfirst($liveOfferData['packageBlurb']);

		$packageId			= $ticketData['packageId'];
		// 2011-01-05
		$numNights			= $ticketData['numNights'];

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
		$isTaxIncluded      = $ticketData['isTaxIncluded'];

		$checkoutHash		= md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
		$checkoutKey		= base64_encode(serialize(array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)));

		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			if ($ticket['Ticket']['siteId'] == 1) {
				$checkoutLink		= "https://". DEV_USER ."-lldev.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
			} else if ($ticket['Ticket']['siteId'] == 2) {
				$checkoutLink		= "https://". DEV_USER ."-familydev.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
			}
		} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
			if ($ticket['Ticket']['siteId'] == 1) {
				$checkoutLink		= "https://stage-luxurylink.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
			} else if ($ticket['Ticket']['siteId'] == 2) {
				$checkoutLink		= "https://stage-family.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
			}
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

		$offerTypeTxt = $isAuction ? 'Auction' : 'Buy Now';

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

		// reservation info
		$resData = $this->Ticket->query("SELECT * FROM reservation WHERE ticketId = $ticketId ORDER BY reservationId DESC LIMIT 1");
		if (!empty($resData)) {
			$resConfNum = $resData[0]['reservation']['reservationConfirmNum'];
			$resArrivalDate = date('M d, Y', strtotime($resData[0]['reservation']['arrivalDate']));
			$resDepartureDate = date('M d, Y', strtotime($resData[0]['reservation']['departureDate']));
			$resConfToCustomer = empty($resData[0]['reservation']['reservationConfirmToCustomer']) ?
									 $resData[0]['reservation']['created']
									: $resData[0]['reservation']['reservationConfirmToCustomer'];
			$resConfBy = $resData[0]['reservation']['confirmedBy'];
			$resArrDate = $resData[0]['reservation']['arrivalDate'];
			$resDepDate = $resData[0]['reservation']['departureDate'];
			// 07/06/11 - jwoods added
			$resConfirmationNotes = $resData[0]['reservation']['confirmationNotes'];
		}
		// cancellation info
		$ppvNoticeData = $this->Ticket->query("SELECT * FROM ppvNotice WHERE ticketId = $ticketId and ppvNoticeTypeId = 29 ORDER BY created DESC LIMIT 1");
		// you cannot send out cancellation confirmed email unless cancellation request email has been sent
		if(!empty($ppvNoticeData)) {
			$ppvNoticeCreatedDate = date('M d, Y', strtotime($ppvNoticeData[0]['ppvNotice']['created']));
			$canData = $this->Ticket->query("SELECT * FROM cancellation WHERE ticketId = $ticketId ORDER BY cancellationId DESC LIMIT 1");
			if (!empty($canData)) {
				$canConfNum = $canData[0]['cancellation']['cancellationNumber'];
				$canConfBy = $canData[0]['cancellation']['confirmedBy'];
				$canNote = $canData[0]['cancellation']['cancellationNotes'];
				$canConfDate = date('M d, Y', strtotime($canData[0]['cancellation']['created']));
			}
		}

		//follow up email sent
		$ppvNoticeData = $this->Ticket->query("SELECT emailSentDatetime FROM ppvNotice WHERE ticketId = $ticketId and ppvNoticeTypeId = 2 ORDER BY created DESC LIMIT 1");
		$emailSentDatetime = (!empty($ppvNoticeData[0]['ppvNotice']['emailSentDatetime'])) ?
								date('M d, Y', strtotime($ppvNoticeData[0]['ppvNotice']['emailSentDatetime'])) : "";

		// cc variables
		// -------------------------------------------------------------------------------
		if (is_array($userPaymentData) && !empty($userPaymentData)) {
			$ccFour				= substr(aesDecrypt($userPaymentData['UserPaymentSetting']['ccNumber']), -4, 4);
			$ccType				= $userPaymentData['UserPaymentSetting']['ccType'];
		}

		// guarantee amount
		// -------------------------------------------------------------------------------
		$guarantee = false;

		// 2011-05-03 jwoods - guarantee check
		if ($ticketData['guaranteeAmt'] && is_numeric($ticketData['guaranteeAmt']) && ($ticketData['guaranteeAmt'] > 0)) {
			if ($ticketData['billingPrice'] < $ticketData['guaranteeAmt']) {
			    $guarantee = $this->numF($ticketData['guaranteeAmt']);
			}
		}

        // guarantee check prior to 2011-05-03 changes
        if (!$guarantee) {
			if ($liveOfferData['reserveAmt'] && is_numeric($liveOfferData['reserveAmt']) && ($liveOfferData['reserveAmt'] > 0)) {
				if ($ticketData['billingPrice'] < $liveOfferData['reserveAmt']) {
					$guarantee = $this->numF($liveOfferData['reserveAmt']);
				}
			}
			if ($offerLive['isMystery']) {
				$guarantee = $this->numF($liveOfferData['reserveAmt']);
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

        // acarney 2010-12-08
        // Making it so that confirmation template is multi-client aware
        // TODO: investigate impact of using array for client variables in other templates
        // to avoid duplication of code
        $isMultiClientPackage = (count($clients) > 1) ? true : false;
        if ($isMultiClientPackage) {
            $clientVars = array();
            foreach ($clients as $i => $client) {
                $clientVars[$i]['clientId']		        = $client['clientId'];
                $clientVars[$i]['parentClientId'] 	    = $client['parentClientId'];
                $clientVars[$i]['clientNameP'] 		    = $client['name'];
                $clientVars[$i]['clientName'] 		    = $client['contacts'][0]['ppv_name'];
                $clientVars[$i]['oldProductId']		    = $client['oldProductId'];
                $clientVars[$i]['locationDisplay']	    = $client['locationDisplay'];
                $clientVars[$i]['clientPrimaryEmail']   = $client['contact_to_string'];
                $clientVars[$i]['clientCcEmail'] 		= $client['contact_cc_string'];
                $clientVars[$i]['clientAdjustedPrice']  = $this->numF(($client['percentOfRevenue'] / 100) * $ticketData['billingPrice']);
            }
        }
        $clientId			    = $clients[$client_index]['clientId'];
        $parentClientId 	    = $clients[$client_index]['parentClientId'];
        $clientNameP 		    = $clients[$client_index]['name'];
        $clientName 		    = $clients[$client_index]['contacts'][0]['ppv_name'];
        $oldProductId		    = $clients[$client_index]['oldProductId'];
        $locationDisplay	    = $clients[$client_index]['locationDisplay'];
        $clientPrimaryEmail     = $clients[$client_index]['contact_to_string'];
        $clientCcEmail 		    = $clients[$client_index]['contact_cc_string'];
        $clientAdjustedPrice    = $this->numF(($clients[$client_index]['percentOfRevenue'] / 100) * $ticketData['billingPrice']);

		// added June 17 -- to allow copy for LL Auc Winner Email and Res Confirmed Email
		if (in_array($ppvNoticeTypeId, array(1,18))) {
			$primaryDest = $this->Ticket->getTicketDestStyleId($ticketId);
			// ticket 1459 jwoods - $removed rentalCarCopy and $bookFlightCopy
			// if (in_array(20, $primaryDest)) {
			//	$rentalCarCopy = file_get_contents('../vendors/email_msgs/email_includes/rentalcarcopy_uki.html');
			// } elseif (in_array(4, $primaryDest)) {
			// 	$rentalCarCopy = file_get_contents('../vendors/email_msgs/email_includes/rentalcarcopy_europe.html');
			// } elseif (in_array(17,$primaryDest)){
			//	$bookFlightCopy = file_get_contents('../vendors/email_msgs/email_includes/bookflightcopy_southamerica.html');
			// }
		}

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
				if (!$checkoutLink) {
					$checkoutLink		= "https://www.luxurylink.com/my/my_purchase.php?z=$checkoutKey";
				}

				// auction facilitator
				// -------------------------------------------------------------------------------
				$dateRequestLink = "https://www.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
					$dateRequestLink = "https://". DEV_USER  ."-lldev.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
					$dateRequestLink = "https://stage-luxurylink.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				}

				break;
			case 2:
				$siteName = 'FamilyGetaway.com';
				$siteDisplay = 'FamilyGetaway.com';
				$siteEmail = 'familygetaway.com';
				$siteUrl = 'http://www.familygetaway.com/';
				$siteHeader = 'DE6F0A';
				$sitePhone  = '(877) 372-5877';
				$sitePhoneLocal = '(310) 956-3703';
				$siteFax = '(800) 440-3820';
				$headerLogo = 'http://www.luxurylink.com/images/family/logo_emails.gif';
				if (!$checkoutLink) {
					$checkoutLink		= "https://www.familygetaway.com/my/my_purchase.php?z=$checkoutKey";
				}

				// auction facilitator
				// -------------------------------------------------------------------------------
				$dateRequestLink = "https://www.familygetaway.com/my/my_date_request.php?tid=$ticketId";
				if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
					$dateRequestLink = "https://". DEV_USER  ."-familydev.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
					$dateRequestLink = "https://stage-family.luxurylink.com/my/my_date_request.php?tid=$ticketId";
				}

				break;
		}
		$siteId = $ticketData['siteId'];

		// check if already sent out a reservation request
		if (in_array($ppvNoticeTypeId, array(2,10))) {
			$res_request = $this->Ticket->query("SELECT COUNT(*) AS count FROM ppvNotice where ticketId = {$ticketId} AND ppvNoticeTypeId IN (2,10);");
			$res_request_count = $resrequest[0][0]['count'];
		}

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
			case 26:
				// general customer template
				include('../vendors/email_msgs/notifications/26_general_customer_template.html');
				$emailSubject = "Your $siteName Booking is Confirmed - $clientNameP";
				$emailFrom=($isAuction)?"$siteDisplay<resconfirm@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resconfirm@$siteEmail" : "reservations@$siteEmail";
				break;
			case 23:
				// send out res confirmation to client also as copy
				include('../vendors/email_msgs/ppv/23_conf_copy_client.html');
				$emailSubject = "$siteName Booking Confirmed for $userFirstName $userLastName - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<resconfirm@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resconfirm@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				break;
			case 2:
				// send out res request
				$extranet_link = $this->getExtranetLink($ticketId, $siteId);
				include('../vendors/email_msgs/notifications/2_reservation_request.html');
				$emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				if (isset($res_request_count) && $res_request_count > 0) {
					$emailSubject = 'NEW DATES REQUESTED - ' . $emailSubject;
				}

				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 29:
				// send out res cancellation request
				$extranet_link = $this->getExtranetCancellationLink($ticketId, $siteId);
				include('../vendors/email_msgs/notifications/29_reservation_cancel_request.html');
				$emailSubject = "$siteName Cancellation Request - ACTION REQUIRED - $clientName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 30:
				// send out res cancellation confirmation
				include('../vendors/email_msgs/notifications/30_reservation_cancel_confirmation.html');
				$emailSubject = "Your $siteName Booking was Cancelled. - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				break;
			case 31:
				// send out res cancellation confirmation
				include('../vendors/email_msgs/ppv/cancel_ppv.html');
				$emailSubject = "Your $siteName Booking was Cancelled. - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				break;
			case 25:
				// send out res request w/o xnet
				include('../vendors/email_msgs/notifications/25_res_request_no_xnet.html');
				$emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 27:
				// general client template
				include('../vendors/email_msgs/notifications/27_general_client_template.html');
				$emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 28:
				// general res request template
				include('../vendors/email_msgs/notifications/28_general_res_request_template.html');
				$emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 24:
				// send out res request
				$extranet_link = $this->getExtranetLink($ticketId, $siteId);
				include('../vendors/email_msgs/notifications/24_reservation_request_followup.html');
				$emailSubject = "2nd Request, Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
				$userEmail = $clientPrimaryEmail;
				$emailCc = $clientCcEmail;
				break;
			case 32:
				// this goes out with 24, info to customer
				$extranet_link = $this->getExtranetLink($ticketId, $siteId);
				include('../vendors/email_msgs/notifications/32_reservation_request_followup_customer.html');
				$emailSubject = "Your Pending Reservation";
				$emailFrom = ($isAuction) ? "$siteDisplay<resrequests@$siteEmail>" : "$siteDisplay<reservations@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
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
				$emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $userFirstName $userLastName";
				if (isset($res_request_count) && $res_request_count > 0) {
					$emailSubject = 'NEW DATES REQUESTED - ' . $emailSubject;
				}
				$emailFrom = "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = "exclusives@$siteEmail";
				if ($this->Ticket->isMultiProductPackage($ticketId)) {
					$emailFrom = "$siteDisplay<resrequest@$siteEmail>";
					$emailReplyTo = "resrequest@$siteEmail";
				}
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
				$emailSubject = "Your Dates Were Not Available - $clientNameP";
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
				$emailSubject = "Your $siteName Purchase is Not Complete - Action Required - $clientNameP";
				$emailFrom = "$siteDisplay<auction@$siteEmail>";
				$emailReplyTo = "auction@$siteEmail";
				break;
			case 20:
				include('../vendors/email_msgs/notifications/20_auction_your_dates_received.html');
				$emailSubject = "Your $siteName Request has been Received - $clientNameP";
				$emailFrom = ($isAuction) ? "$siteDisplay<auction@$siteEmail>" : "$siteDisplay<exclusives@$siteEmail>";
				$emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
				break;
			case 33:
				include('../vendors/email_msgs/notifications/33_change_dates_request_template.html');
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
			if (trim($override_email_subject)) {
				$emailSubject = $override_email_subject;
			}

			$this->sendPpvEmail($userEmail, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials, $email_attachment, $email_attachment_type);

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
					$this->sendPpvEmail($clientPrimaryEmail, $emailFrom, $clientCcEmail, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId);
				}
			}
		}

		// return the string for toolbox ppvNotice add screen (manual edit and send)
		// -------------------------------------------------------------------------------
		if ($returnString) {
			return $emailBody;
		}
	}

	function getExtranetLink($ticketId, $siteId) {

		if (!$ticketId || !is_numeric($ticketId)) {
			return null;
		}

		// generate the link so clients can handle res requests via extranet
		$uri = '/xnet/services/rd.php';

		if ($siteId == 1) {
			$host = 'http://www.luxurylink.com';
			if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
				$host = 'http://'. DEV_USER .'-lldev.luxurylink.com';
			} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$host = 'http://stage-luxurylink.luxurylink.com';
			}
		} elseif ($siteId == 2) {
			$host = 'http://www.familygetaway.com';
			if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
				$host = 'http://' . DEV_USER .  '-familydev.luxurylink.com';
			} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$host = 'http://stage-family.luxurylink.com';
			}
		}

		$ts = strtotime('NOW');
		$ticketIdHash = base64_encode($ticketId);
		$tsHash = base64_encode($ts);

		$hash = md5($ticketId . $ts . 'L33T-KEY-XTRANET');

		return $host . $uri . "?z=$hash&t=$ticketIdHash&ts=$tsHash";
	}

	function getExtranetCancellationLink($ticketId, $siteId) {

		if (!$ticketId || !is_numeric($ticketId)) {
			return null;
		}

		// generate the link so clients can handle res requests via extranet
		$uri = '/xnet/services/rcc.php';

		if ($siteId == 1) {
			$host = 'http://www.luxurylink.com';
			if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
				$host = 'http://'. DEV_USER .'-lldev.luxurylink.com';
			} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$host = 'http://stage-luxurylink.luxurylink.com';
			}
		} elseif ($siteId == 2) {
			$host = 'http://www.familygetaway.com';
			if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
				$host = 'http://'. DEV_USER .'-familydev.luxurylink.com';
			} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
				$host = 'http://stage-family.luxurylink.com';
			}
		}

		$ts = strtotime('NOW');
		$ticketIdHash = base64_encode($ticketId);
		$tsHash = base64_encode($ts);

		$hash = md5($ticketId . $ts . 'L33T-KEY-XTRANET');

		return $host . $uri . "?z=$hash&t=$ticketIdHash&ts=$tsHash";
	}

	function sendPpvEmail($emailTo, $emailFrom, $emailCc, $emailBcc, $emailReplyTo, $emailSubject, $emailBody, $ticketId, $ppvNoticeTypeId, $ppvInitials, $email_attachment, $email_attachment_type) {

		if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
			$appendDevMessage = "---- DEV MAIL ---- \n<br />ORIGINAL TO:  $emailTo\n<br />ORIGINAL CC: $emailCc\n<br />ORIGINAL BCC: $emailBcc";
			$emailTo = $emailCc = $emailBcc = 'devmail@luxurylink.com';
			$emailBody = $appendDevMessage . $emailBody;
			$emailBody.= print_r($_SERVER, true);
			$emailSubject = "DEV - " . $emailSubject . $fname;
		}

		// send out ppv and winner notification emails
		// -------------------------------------------------------------------------------

        //original before hb updates
        //$emailHeaders = "From: $emailFrom\r\n";
		//$emailHeaders.= "Cc: $emailCc\r\n";
		//$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
		//$emailHeaders.= "Bcc: $emailBcc\r\n";
    	//$emailHeaders.= "Content-type: text/html\r\n";

        //boundary variable

        $fname = $email_attachment;
        $ftype = $email_attachment_type;
        if ($fname) {

        $num = md5(time());

        $emailHeaders = "From: $emailFrom\n";
		$emailHeaders .= "Cc: $emailCc\n";
		$emailHeaders .= "Reply-To: $emailReplyTo\n";
		$emailHeaders .= "Bcc: $emailBcc\n";
        $emailHeaders .= "MIME-Version: 1.0\n";
        $emailHeaders .= "Content-Type: multipart/mixed; ";
        $emailHeaders .= "boundary=".$num."\n";

        // This two steps to help avoid spam
        $emailHeaders .= "--".$num."\n";
        $emailHeaders .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">\n";
        $emailHeaders .= "X-Mailer: PHP v".phpversion()."\n";

        // With message
        $emailHeaders .= "Content-Type: text/html;\n";
        $emailHeaders .= "Content-Transfer-Encoding: 8bit \n";
        $emailHeaders .= $emailBody."\n\n";

        //get attachments and loop thru process to create headers for each file, open file read binary

            foreach ($fname as $key => $value)
            {
                $filename = $_SERVER{'DOCUMENT_ROOT'} . '/attachments/' . $value;
                $fp = fopen($filename, "rb");
                $file = fread($fp, filesize($filename));
                $file = chunk_split(base64_encode($file));
                fclose($fp);

                $emailHeaders .= "--".$num."\n";
                $emailHeaders .= "Content-Type:".$ftype[$key]."; ";
                $emailHeaders .= "name=\"".$value."\"\n";
                $emailHeaders .= "Content-Transfer-Encoding: base64\n";
                $emailHeaders .= "Content-Disposition: attachment; ";
                $emailHeaders .= "filename=\"".$value."\"\n\n";
                $emailHeaders .= $file."\n";
            }
        } else {
            //original before hb updates
            $emailHeaders = "From: $emailFrom\r\n";
    		$emailHeaders.= "Cc: $emailCc\r\n";
    		$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
    		$emailHeaders.= "Bcc: $emailBcc\r\n";
        	$emailHeaders.= "Content-type: text/html\r\n";
        }

		// 06/16/11 jwoods - relay through Silverpop
		// if ($ppvNoticeTypeId == 18 && (strpos($emailBody, 'Testerosa') > 0)) {
		if ($ppvNoticeTypeId == 18) {
			$this->sendSilverpopRelay('ppv_auction_winner', $emailFrom, $emailReplyTo, $emailTo, $emailSubject, $emailBody);
		} else {
			@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
		}

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
		if ($ppvNoticeTypeId == 1 || $ppvNoticeTypeId == 23) {
			// reservation confirmation from buy now with seasonal pricing
			$currentTicketStatus = $this->Ticket->query("SELECT ticketStatusId as tsi FROM ticket WHERE ticketId = {$ticketId}");
			if($currentTicketStatus[0]['tsi'] == 14 )
				$newTicketStatus = 14;
			else
				$newTicketStatus = 4; //auction or FP

			$resData = $this->Ticket->query("SELECT * FROM reservation WHERE ticketId = $ticketId ORDER BY reservationId DESC LIMIT 1");
			if (!empty($resData)) {
				$reservationId = $resData[0]['reservation']['reservationId'];
				$reservation = array();
				$reservation['reservationId'] = $reservationId;
				$reservation['ticketId'] = $ticketId;
				$reservation['reservationConfirmToCustomer'] = date('Y:m:d H:i:s', strtotime('now'));
				$this->Reservation->save($reservation);
			}
		} elseif (in_array($ppvNoticeTypeId, array(2,25,33))) {
			// send ticket status to RESERVATION REQUESTED
			$newTicketStatus = 3;
		} elseif ($ppvNoticeTypeId == 10) {
			#$newTicketStatus = 1;
			$newTicketStatus = 12;
		} elseif ($ppvNoticeTypeId == 14) {
			// DATES NOT AVAILABLE
			$newTicketStatus = 11;
		}  elseif ($ppvNoticeTypeId == 29) {
			// Ticket cancellation request
			$newTicketStatus = 16;
		} elseif ($ppvNoticeTypeId == 30) {
			// Ticket cancellation confirmation
			$newTicketStatus = 17;
		} elseif ($ppvNoticeTypeId == 24) {
			// Res follow up (for FP)
			$newTicketStatus = 9;
		} elseif ($ppvNoticeTypeId == 28) {
			// ticket #2243
			$newTicketStatus = 19;
		}

		if ($newTicketStatus) {
			$this->updateTicketStatus($ticketId, $newTicketStatus);
		}
	}

	function sendSilverpopRelay($xheader, $fromAddr, $fromReplyTo, $toAddr, $subj, $msgBody) {
			App::import('Vendor', 'PHPMailerNew', array('file' => 'phpmailernew'.DS.'phpmailer.inc.php'));
			$mail = new PHPMailer();

			$fromArray = explode('<', $fromAddr);
			$mail->IsSMTP();
			$mail->Host = "transact2.silverpop.com";
			$mail->From = str_replace('>', '', $fromArray[1]);
			$mail->FromName = $fromArray[0];
			$mail->AddAddress($toAddr);
			$mail->AddReplyTo($fromReplyTo, $fromReplyTo);
			$mail->WordWrap = 50;    // set word wrap
			$mail->IsHTML(true);    // set email format to HTML
			$mail->xheader = $xheader;
			$mail->Subject = $subj;
			$mail->Body = $msgBody;
			$mail->SMTPDebug = 0;

			try {
				ob_start(); // any output messes up web service call
				$result = $mail->Send(); // send message
				ob_end_clean();
				// removed 08/15/11
				// possibly add receipt to shared folder if we need to keep tabs on Silverpop
				// $mailinfo = print_r($mail,true);
				// $emailHeaders = "From: jwoods@luxurylink.com\n";
				// @mail('jwoods@luxurylink.com', 'ppv silverpop success', $mailinfo, $emailHeaders);
			}
			catch (Exception $e) {
				$emailHeaders = "From: jwoods@luxurylink.com\n";
				@mail('jwoods@luxurylink.com', 'ppv silverpop error', $e->getMessage(), $emailHeaders);
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

		// DEV NO CHARGE
		// ---------------------------------------------------------------------------
		$isDev = false;
		if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage') ||
			stristr($_SERVER['HTTP_HOST'], 'alee') || stristr($_SERVER['HTTP_HOST'], 'alee')) {
			$this->isDev = $isDev = true;
		}

		// also check server env
		if (in_array($_SERVER['ENV'], array('development','staging'))) {
			$this->isDev = $isDev = true;
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

		$paymentProcessorName = $this->PaymentProcessor->find('first', array('conditions' => array('PaymentProcessor.paymentProcessorId' => $data['paymentProcessorId'])));
		$paymentProcessorName = $paymentProcessorName['PaymentProcessor']['paymentProcessorName'];
		
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

			// used promo or gc or cof that resulted in complete ticket price coverage -- no cc charge needed
			// -------------------------------------------------------------------------------
			if ($promoGcCofData['applied'] && ($promoGcCofData['final_price'] == 0)) {
				return $this->runPostChargeSuccess($ticket, $data, $usingUpsId, $userPaymentSettingPost, $promoGcCofData, $toolboxManualCharge);
			}
		}

		$paymentDetail = array();
		
		$paymentDetail['ticketId']				= $ticket['Ticket']['ticketId'];
		$paymentDetail['userId']				= $ticket['Ticket']['userId'];
		$paymentDetail['autoProcessed']			= $data['autoCharge'];
		$paymentDetail['initials']				= $data['initials'];
		$paymentDetail['ppBillingAmount']		= $totalChargeAmount;
		$paymentDetail['paymentProcessorId']	= $data['paymentProcessorId'];
		$paymentDetail['paymentTypeId']			= $data['paymentTypeId'];
		$paymentDetail['paymentAmount']			= $data['paymentAmount'];
		$paymentDetail['userPaymentSettingId']	= ($usingUpsId) ? $data['userPaymentSettingId'] : '';
		$ticket['Ticket']['billingPrice'] 		= $totalChargeAmount;
		
		$otherCharge = 0;
		
		if ($data['paymentTypeId'] == 1) {
			// set total charge amount to send to processor
			// ---------------------------------------------------------------------------
			// init payment processing and submit payment
			// ---------------------------------------------------------------------------
			$processor = new Processor($paymentProcessorName);
			$processor->InitPayment($userPaymentSettingPost, $ticket);
			
			if (!$isDev) {
				// do not charge on dev or stage. For Production - charge away!
				$processor->SubmitPost();
			}
			
			$userPaymentSettingPost['UserPaymentSetting']['expMonth'] = str_pad($userPaymentSettingPost['UserPaymentSetting']['expMonth'], 2, '0', STR_PAD_LEFT);

			$paymentDetail 							= array_merge($paymentDetail,$processor->GetMappedResponse());
			$paymentDetail['paymentTypeId'] 		= 1;
			$paymentDetail['ppFirstName']			= $data['firstName'];
			$paymentDetail['ppLastName']			= $data['lastName'];
			$paymentDetail['ppBillingAddress1']		= $userPaymentSettingPost['UserPaymentSetting']['address1'];
			$paymentDetail['ppBillingCity']			= $userPaymentSettingPost['UserPaymentSetting']['city'];
			$paymentDetail['ppBillingState']		= $userPaymentSettingPost['UserPaymentSetting']['state'];
			$paymentDetail['ppBillingZip']			= str_replace(' ', '', $userPaymentSettingPost['UserPaymentSetting']['postalCode']);
			$paymentDetail['ppBillingCountry']		= str_replace(' ', '', $userPaymentSettingPost['UserPaymentSetting']['country']);
			$paymentDetail['ppCardNumLastFour']		= substr($userPaymentSettingPost['UserPaymentSetting']['ccNumber'], -4, 4);
			$paymentDetail['ppExpMonth']			= $userPaymentSettingPost['UserPaymentSetting']['expMonth'];
			$paymentDetail['ppExpYear']				= $userPaymentSettingPost['UserPaymentSetting']['expYear'];
			$paymentDetail['ccType']				= $userPaymentSettingPost['UserPaymentSetting']['ccType'];
			if ($isDev) {
				$paymentDetail['isSuccessfulCharge']				= 1;
			}
		} else {
			$otherCharge = 1;
			if ($data['paymentTypeId'] == 2) {
				// Gift cert
				$longWord = "GIFT CERT";
				$medWord  = "GIFT";
				$shortWord = "GC";
			} elseif ($data['paymentTypeId'] == 3) {
				// Credit on file
				$longWord = "CREDIT ON FILE";
				$medWord  = "CRED";
				$shortWord = "CR";
			} else {
				return '115';
			}

			$paymentDetail['ccType'] 		 		= $shortWord;
			$paymentDetail['userPaymentSettingId'] 	= '';
			$paymentDetail['isSuccessfulCharge']	= 1;
			$paymentDetail['autoProcessed']			= 0;
			$paymentDetail['ppFirstName']			= $data['firstName'];
			$paymentDetail['ppLastName']			= $data['lastName'];
			$paymentDetail['ppResponseDate']		= date('Y-m-d H:i:s', strtotime('now'));
			$paymentDetail['ppCardNumLastFour']		= $medWord;
			$paymentDetail['ppExpMonth']			= $shortWord;
			$paymentDetail['ppExpYear']				= $medWord;
			$paymentDetail['ppBillingAddress1']		= $longWord;
			$paymentDetail['ppBillingCity']			= $longWord;
			$paymentDetail['ppBillingState']		= $longWord;
			$paymentDetail['ppBillingZip']			= $longWord;
			$paymentDetail['ppBillingCountry']		= $longWord;
		}

		// save the response from the payment processor
		// ---------------------------------------------------------------------------
		$this->PaymentDetail->create();
		if (!$this->PaymentDetail->save($paymentDetail)) {
			@mail('devmail@luxurylink.com,rvella@luxurylink.com', 'WEB SERVICE ERROR: PAYMENT PROCESSED BUT NOT SAVED', print_r($this->PaymentDetail->validationErrors,true)  . print_r($paymentDetail, true));
		}

		// return result whether success or denied
		// ---------------------------------------------------------------------------
		if ((isset($processor) && $processor->ChargeSuccess()) || $isDev || $otherCharge) {
			return $this->runPostChargeSuccess($ticket, $data, $usingUpsId, $userPaymentSettingPost, $promoGcCofData, $toolboxManualCharge);
		} else {
			if ($data['paymentProcessorId'] == 1) {
				return $processor->GetResponseTxt();
			} else {
				return false;
			}
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
                    else {
                        mail('devmail@luxurylink.com', $ticket['Ticket']['ticketId'] . ' ticket track detail not saved: missing site id', print_r($ticket, true));
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
		if (isset($promoGcCofData['GiftCert']) && $promoGcCofData['GiftCert'] && $promoGcCofData['GiftCert']['applied']) {
			$this->PaymentDetail->saveGiftCert($ticket['Ticket']['ticketId'], $promoGcCofData['GiftCert'], $ticket['Ticket']['userId'], $data['autoCharge'], $data['initials']);
		} elseif (isset($promoGcCofData['Cof']) && $promoGcCofData['Cof'] && $promoGcCofData['Cof']['applied']) {
			$this->PaymentDetail->saveCof($ticket['Ticket']['ticketId'], $promoGcCofData['Cof'], $ticket['Ticket']['userId'], $data['autoCharge'], $data['initials'],false);
		}
		
		$this->Ticket->save($ticketStatusChange);

		// ********* SITE NAME **********
		switch ($ticket['Ticket']['siteId']) {
			case 1:
				$siteName = 'Luxury Link';
				$url = 'http://www.luxurylink.com';
				$emailFrom = $emailReplyTo = 'referafriend@luxurylink.com';
				$headerLogo = 'http://www.luxurylink.com/images/ll_logo_2009_2.gif';
				break;
			case 2:
				$siteName = 'Family';
				$url = 'http://www.familygetaway.com';
				$emailFrom = $emailReplyTo = 'referafriend@familygetaway.com';
				$headerLogo = 'http://www.luxurylink.com/images/family/logo.gif';
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
