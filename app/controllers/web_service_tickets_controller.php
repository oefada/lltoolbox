<?php
Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');
App::import("Vendor", "Base", array('file' => "appshared" . DS . "framework" . DS . "Base.php"));
require_once(APP . '/vendors/pp/Processor.class.php');

error_reporting(E_ALL);
set_error_handler("wstErrorHandler");
register_shutdown_function('wstErrorShutdown');

class WebServiceTicketsController extends WebServicesController
{
    public $name = 'WebServiceTickets';

    public $components = array('PackageIncludes', 'LltgServiceHelper');

    public $uses = array(
        'Ticket',
        'UserPaymentSetting',
        'PaymentDetail',
        'Client',
        'User',
        'Offer',
        'Bid',
        'ClientLoaPackageRel',
        'Track',
        'OfferType',
        'Loa',
        'TrackDetail',
        'PpvNotice',
        'Address',
        'OfferLuxuryLink',
        'SchedulingMaster',
        'SchedulingInstance',
        'Reservation',
        'PromoTicketRel',
        'Promo',
        'TicketReferFriend',
        'Package',
        'PaymentProcessor',
        'CakeLog',
        'ClientThemeRel',
        'Image',
        'ImageClient',
        'CreditTracking',
        'CreditBank',
        'EventRegistryDonor',
        'EventRegistryGiftFailure',
        'MailingList',
        'ReservationPreferDateFromHotel',
        'PgBooking',
        'PgPayment'
    );

    public $serviceUrl = '/web_service_tickets';
    public $errorResponse = false;
    public $errorMsg = false;
    public $errorTitle = false;

    // nusoap.php needs this
    public $api = array(
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
        'sendPpvEmail' => array(
            'input' => array(
                'emailTo' => 'xsd:string',
                'emailFrom' => 'xsd:string',
                'emailCc' => 'xsd:string',
                'emailBcc' => 'xsd:string',
                'emailReplyTo' => 'xsd:string',
                'emailSubject' => 'xsd:string',
                'emailBody' => 'xsd:string',
                'ticketId' => 'xsd:int',
                'ppvNoticeTypeId' => 'xsd:int',
                'ppvInitials' => 'xsd:string',
            ),
            'output' => array(
                'return' => 'xsd:boolean',
            ),
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
        'processPaymentGift' => array(
            'doc' => 'N/A',
            'input' => array('in0' => 'xsd:string'),
            'output' => array('return' => 'xsd:string')
        ),
        'processGiftPostchargeSuccess' => array(
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
        ),
        'getTicketAppliedPayment' => array(
            'doc' => 'N/A',
            'input' => array('in0' => 'xsd:string'),
            'output' => array('return' => 'xsd:string')
        )
    );

    /**
     *
     */
    public function beforeFilter()
    {
        $this->LdapAuth->allow('*');
    }

    /**
     * for client specific tweaks of text before sending to client.
     * eg http://comindwork/web2.aspx/ROADMAP/PROC/TICKET#keeperMatt:2351
     * We're surroungind client specific text with the html comments so the customer doesn't see it
     * and we strip the tags out before sending to the client
     *
     * @param $packageIncludes
     * @return mixed
     */
    public function cleanUpPackageIncludes($packageIncludes)
    {
        $packageIncludes = str_replace("<!--clientonlystart ", "", $packageIncludes);
        $packageIncludes = str_replace(" clientonlyend-->", "", $packageIncludes);
        return $packageIncludes;
    }

    /**
     * see $this->processTicket()
     *
     * @param $in0
     * @return string
     */
    public function processNewTicket($in0)
    {
        $json_decoded = json_decode($in0, true);
        $this->errorResponse = $this->errorMsg = $this->errorTitle = false;
        // The ISDEV and ISSTAGE vars are set in core.php and they rely on $_SERVER, which isn't set
        // when run as a cron/shell.
        // Get the environment this rpc was called from - set in cron_auction_closing.php
        $env = isset($json_decoded['env']) ? $json_decoded['env'] : false;
        define('CRON_ENV', $env);

        unset($json_decoded['env']);
        unset($json_decoded['debug']);

        if (!$this->processTicket($json_decoded)) {
            $server_type = '';
            if (defined('ISDEV') && !defined('ISSTAGE')) {
                $server_type = '[DEV] --> ';
            } else {
                if (defined('ISSTAGE')) {
                    $server_type = '[STAGE] --> ';
                }
            }
            @mail(
                'devmail@luxurylink.com',
                "$server_type" . 'WEBSERVICE (TICKETS): ERROR (' . $this->errorResponse . ')' . $this->errorTitle,
                $this->errorMsg . "<br /><br />\n\n" . print_r($json_decoded, true)
            );
            return 'FAIL';
        } else {
            return 'SUCCESS';
        }

    }

    public function getPromoGcCofData($in0)
    {
        $data = json_decode($in0, true);
        if (!empty($data) && isset($data['ticketId']) && isset($data['billingPrice'])) {
            return json_encode($this->Ticket->getPromoGcCofData($data['ticketId'], $data['billingPrice']));
        }
        return '0';
    }


    public function processFixedPriceTicket($ticketData)
    {
        if (!$ticketData['ticketId']) {
            $this->errorResponse = 2001;
            $this->errorTitle = 'Missing Ticket ID';
            $this->errorMsg = 'Fixed Price Ticket processing was aborted due to receiving invalid data.';
            return false;
        }

        // send out fixed price request emails
        // -------------------------------------------------------------------------------
        $params = array();
        $params['ticketId'] = $ticketData['ticketId'];
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'AUTO';

        // send out fixed price emails
        // -------------------------------------------------------------------------------
        if (isset($ticketData['siteId']) && $ticketData['siteId'] == 2) {
            $params['ppvNoticeTypeId'] = 20; // Fixed Price - Winner Notification
        } else {
            $params['ppvNoticeTypeId'] = 12; // Fixed Price - Winner Notification
        }

        $this->ppv(json_encode($params));

        //special request
        if (trim($ticketData['requestNotes'])) {
            $params['ppvNoticeTypeId'] = 10; // Fixed Price - Client Exclusive Email
        } else {
            #$params['ppvNoticeTypeId'] = 25;     // Reservation Request w/ no xnet (to be removed)
            $params['ppvNoticeTypeId'] = 2; // Reservation Request new one with xnet
        }

        // check reservation checkin date - if 48 hrs send ppvid 10
        $arrival_date_1 = $ticketData['requestArrival'] && $ticketData['requestArrival'] != '0000-00-00' ? @strtotime(
            $ticketData['requestArrival']
        ) : false;
        $arrival_date_2 = $ticketData['requestArrival2'] && $ticketData['requestArrival2'] != '0000-00-00' ? @strtotime(
            $ticketData['requestArrival2']
        ) : false;

        $arrival_within_2_days = strtotime('+2 DAYS'); // 48 hrs from now
        if ($arrival_date_1 > 0 && $arrival_date_1 <= $arrival_within_2_days) {
            $params['ppvNoticeTypeId'] = 10;
        }
        if ($arrival_date_2 > 0 && $arrival_date_2 <= $arrival_within_2_days) {
            $params['ppvNoticeTypeId'] = 10;
        }
        //if multi-product offer, then send old res request w/o client res xtranet
        if ($this->Ticket->isMultiProductPackage($params['ticketId'])) {
            $params['ppvNoticeTypeId'] = 10; // old res request
        }
        $expirationCriteriaId = $this->Ticket->getExpirationCriteria($params['ticketId']);
        if ($expirationCriteriaId == 5) {
            // this is retail value
            $params['ppvNoticeTypeId'] = 10; // old res request
        }
        //if request comes in for more than the package NumNights, same as special request
        //acarney 2011-01-18 -- disabling the following block of code because we do not allow
        //users to enter their own departure dates anymore

        $package = $this->Package->read(null, $ticketData['packageId']);
        if (0) {
            $interval1 = (strtotime($ticketData['requestDeparture']) - strtotime(
                        $ticketData['requestArrival']
                    )) / 86400;
            if ($interval1 > $package['Package']['numNights']) {
                $params['ppvNoticeTypeId'] = 10; // old res request
            }

            if ($ticketData['requestArrival2'] && $ticketData['requestArrival2'] != '000-00-00') {
                $interval2 = (strtotime($ticketData['requestDeparture2']) - strtotime(
                            $ticketData['requestArrival2']
                        )) / 86400;
                if ($interval2 > $package['Package']['numNights']) {
                    $params['ppvNoticeTypeId'] = 10; // old res request
                }
            }
        }

        $this->ppv(json_encode($params));

        $params['ppvNoticeTypeId'] = 11; // Fixed Price - Internal Exclusive Email
        $this->ppv(json_encode($params));

        // return ticket id to the frontend live site
        // -------------------------------------------------------------------------------
        return true;
    }

    /**
     * see processNewTicket() - it calls this method
     *
     * @param $data
     * @return bool
     */
    private function processTicket($data)
    {
        // TODO optimize and implement error handler class

        // if we do not have these values then void
        // -------------------------------------------------------------------------------
        if (empty($data) || !is_array($data)) {
            $this->errorResponse = 1101;
            $this->errorTitle = 'Invalid Data';
            $this->errorMsg = 'Ticket processing was aborted due to receiving invalid data.';
            $this->logError(__METHOD__);
            return false;
        }
        if (!isset($data['ticketId']) || empty($data['ticketId'])) {
            $this->errorResponse = 1102;
            $this->errorTitle = 'Invalid Data';
            $this->errorMsg = 'Ticket processing was aborted because the required field ticketId was not supplied.';
            $this->logError(__METHOD__);
            return false;
        }
        if (!isset($data['userId']) || empty($data['userId'])) {
            $this->errorResponse = 1103;
            $this->errorTitle = 'Invalid Data';
            $this->errorMsg = 'Ticket processing was aborted because the required field userId was not supplied.';
            $this->logError(__METHOD__);
            return false;
        }
        if (!isset($data['offerId']) || empty($data['offerId'])) {
            $this->errorResponse = 1104;
            $this->errorTitle = 'Invalid Data';
            $this->errorMsg = 'Ticket processing was aborted because the required field offerId was not supplied.';
            $this->logError(__METHOD__);
            return false;
        }

        // fetches a Group, its domain, its associated Users, and the Users' associated Articles
        $this->Offer->recursive = 2;
        // schedulingMaster info will be contained in $offerData
        $offerData = $this->Offer->read(null, $data['offerId']);

        // joins on client table and clientLoaPackageRel
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
            // offerId will be unique, however, it is not defined as unique in offerLuxuryLink or offerFamily.
            // offerId is defined as unique in 'offer' table where it is generated, thus, it is unique
            // offerId is not unique in the offerLuxuryLink or offerFamily tables:
            //SELECT COUNT(DISTINCT(offerId)) FROM offerLuxuryLink;//712,672 rows
            //SELECT COUNT(*) FROM offerLuxuryLink;//719,825 rows
            $offerLive = $this->Offer->query("SELECT * FROM $ticketSite WHERE offerId = " . $data['offerId']);
            $offerLive = $offerLive[0][$ticketSite];
        } else {
            $this->errorTitle = 'Invalid Site';
            $this->errorMsg = 'Ticket processing was aborted because site was not supplied.';
            $this->logError(__METHOD__);
            return false;
        }

        // $ticket_toolbox contains data specific to the the purchase as contained in 'ticket' table.
        // eg. user data, departure times, packageId etc
        $ticket_toolbox = $this->Ticket->read(null, $data['ticketId']);

        // in case of dup tickets or if ticket was already processed, error out
        if (empty($ticket_toolbox) || !$ticket_toolbox) {
            $this->errorResponse = 187;
            $this->errorTitle = 'Ticket Not Processsed [CHECK REPLICATION]';
            $this->errorMsg = 'Ticket data not replicated to Toolbox yet or TB DB is down.  This ticket has been flagged for reprocessing and will finish the process when the systems come online.';
            $this->logError(__METHOD__);
            return false;
        }

        // if record exists in paymentDetail, this ticket got processed already.
        $ticket_payment = $this->PaymentDetail->query(
            'SELECT * FROM paymentDetail WHERE ticketId = ' . $data['ticketId']
        );

        if (!empty($ticket_payment)) {
            $this->errorResponse = 188;
            $this->errorTitle = 'Payment Already Detected for Ticket';
            $this->errorMsg = 'Stopped processing this ticket.  An existing payment has been detected for this ticket id whether it was successful or not.  This ticket has been marked as processed successfully.';
            $this->logError(__METHOD__);
            return true;
        }

        // all ticket processing happens in here
        // the auction closing cron job sets the the ticket to transmitted=1 when it finishes with it
        // to prevent concurrency issues
        if ($ticket_toolbox['Ticket']['transmitted'] == 0) {
            $ticketId = $data['ticketId'];

            // 2011-05-04 jwoods - fill in ticket.guaranteeAmt if necessary
            // if the purchase price is less than what we agreed to pay the property, calc the difference and store it
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
                $guaranteeAmount = round(
                    $data['billingPrice'] / ((100 - $resultsDiscount[0]['m']['percentDiscount']) / 100)
                );
                // else, was a reserve amount set?
            } elseif (intval($offerLive['reserveAmt']) > 0) {
                $guaranteeAmount = $offerLive['reserveAmt'];
            }

            if ($guaranteeAmount > 0) {
                $this->Ticket->query(
                    "UPDATE ticket SET guaranteeAmt = ? WHERE ticketId = ?",
                    array($guaranteeAmount, $ticketId)
                );
            }

            // update the tracks
            // 'track' table contains info on what should be done with the money coming in with regards to the client
            // eg. is this a 'barter' or 'remitt', how much balance is left unpaid etc.
            $schedulingMasterId = $offerData['SchedulingInstance']['SchedulingMaster']['schedulingMasterId'];
            $smid = $this->Track->query(
                "SELECT trackId FROM schedulingMasterTrackRel WHERE schedulingMasterId = $schedulingMasterId LIMIT 1"
            );
            $smid = $smid[0]['schedulingMasterTrackRel']['trackId'];
            if (!empty($smid)) {
                $this->addTrackPending($smid, $data['billingPrice']);
            }

            // take down future instances of offers if reached package.maxNumSales
            // this exists independent of expirationCriteriaId and may run concurrent with a takedown
            // based on expirationCriteriaId
            // This a take down specific to a pricePointId and the max sales for that pricePointId as
            // opposed to a larger barter/remitt agreement for a client
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
                    $this->Ticket->__runTakeDownRetailValue(
                        $offerLive['clientId'],
                        $offerLive['retailValue'],
                        $ticketId
                    );
                    break;
                case 6: //mbyrnes
                    $this->Ticket->__runTakeDownNumRooms($offerLive, $ticketId, $ticketSite, $data['numNights']);
                    break;

            }

            // find and set promos for this new ticket + refer friend relationship setup
            // -------------------------------------------------------------------------------
            // $promo_data = $this->Ticket->findPromoOfferTrackings($data['userId'], $data['offerId']);
            $promo_data = $this->Ticket->findPromoOfferTrackingsExtended($data['userId'], $data['offerId']);
            if ($promo_data !== false && is_array($promo_data) && !empty($promo_data)) {
                foreach ($promo_data as $promoOfferTracking) {
                    $promo_ticket_rel = array();
                    $promo_ticket_rel['promoCodeId'] = $promoOfferTracking['promoOfferTracking']['promoCodeId'];
                    $promo_ticket_rel['ticketId'] = $ticketId;
                    $promo_ticket_rel['userId'] = $data['userId'];
                    $promo_ticket_rel['creditBlockedFlag'] = $promoOfferTracking['promoOfferTracking']['creditBlockedFlag'];
                    $this->PromoTicketRel->create();
                    $this->PromoTicketRel->save($promo_ticket_rel);
                }
            }

            // additional data for ticket 4002
            $ppInfoLive = $this->Offer->query(
                "SELECT * FROM pricePoint WHERE pricePointId = " . intval($offerLive['pricePointId'])
            );
            $ppInfoLive = $ppInfoLive[0]['pricePoint'];
            $this->Ticket->query(
                "UPDATE ticket SET offerRetailValueUSD = ?, offerRetailValueLocal = ?, offerExtraNightRetailValueUSD = ?, offerExtraNightRetailValueLocal = ? WHERE ticketId = ?",
                array(
                    $offerLive['retailValue'],
                    $ppInfoLive['retailValue'],
                    $offerLive['flexRetailPricePerNight'],
                    $ppInfoLive['flexRetailPricePerNight'],
                    $ticketId
                )
            );


            // if non-auction, just stop here as charging and ppv should not be auto
            // -------------------------------------------------------------------------------
            if (!in_array($data['offerTypeId'], array(1, 2, 6))) {
                return $this->processFixedPriceTicket($data);
            }

            // find out if there is a valid credit card to charge.  charge and send appropiate emails
            // -------------------------------------------------------------------------------
            $user_payment_setting = $this->findValidUserPaymentSetting($data['userId'], $data['userPaymentSettingId']);

            // set ppv params
            // -------------------------------------------------------------------------------
            $ppv_settings = array();

            if (isset($_SERVER['HTTP_HOST']) && stristr($_SERVER['HTTP_HOST'], "dev")) {
                $ppv_settings['override_email_to'] = 'devmail@luxurylink.com';
            }

            $ppv_settings['ticketId'] = $ticketId;
            $ppv_settings['send'] = 1;
            $ppv_settings['manualEmailBody'] = 0;
            $ppv_settings['returnString'] = 0;
            $ppv_settings['initials'] = 'AUTO';

            $auto_charge_card = false;
            if (is_array($user_payment_setting) && !empty($user_payment_setting)) {
                // has valid cc card to charge
                // -------------------------------------------
                $ppv_settings['ppvNoticeTypeId'] = 18; // Auction Winner Email (PPV)
                $auto_charge_card = true;
            } else {
                // has no valid cc on file
                // -------------------------------------------
                $ppv_settings['ppvNoticeTypeId'] = 19; // Auction Winner Email (Declined / Expired CC)
            }

            // set restricted auctions so no autocharging happens
            // -------------------------------------------------------------------------------
            $restricted_auction = false;

            foreach ($clientData as $client) {
                if ($client['Client']['clientTypeId'] == 3 || stristr($client['Client']['name'], 'CRUISE')) {
                    $restricted_auction = true;
                }
            }

            if (stristr($offerLive['offerName'], 'RED') && stristr($offerLive['offerName'], 'HOT')) {
                $restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'FEATURED') && stristr($offerLive['offerName'], 'AUCTION')) {
                $restricted_auction = true;
            }
            if (stristr($offerLive['offerName'], 'AUCTION') && stristr($offerLive['offerName'], 'DAY')) {
                $restricted_auction = true;
            }

            // hack june 29 2010
            if ($clientData[0]['Client']['clientId'] == 378) {
                $restricted_auction = false;
            }
            // do no autocharge restricted auctions. send them old winner notification w/o checkout
            // -------------------------------------------------------------------------------
            if ($restricted_auction) {
                $ppv_settings['ppvNoticeTypeId'] = 5; // Winner Notification (Old one)
                $auto_charge_card = false;
            }

            // check if user has already paid for this ticket
            // -------------------------------------------------------------------------------
            $checkExists = $this->PaymentDetail->query("SELECT * FROM paymentDetail WHERE ticketId = $ticketId");
            if (isset($checkExists[0]['paymentDetail']) && !empty($checkExists[0]['paymentDetail'])) {
                $auto_charge_card = false;
                $ppv_settings['ppvNoticeTypeId'] = 18; // Auction Winner Email (PPV)
                return true;
            }

            $HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : false;
            if (stristr($HTTP_HOST, 'dev') || stristr(
                    $HTTP_HOST,
                    'stage'
                ) || CRON_ENV == 'dev' || CRON_ENV == 'stage'
            ) {
                $auto_charge_card = false;
            }

            $test_card = false;
            $tmpCC = $this->UserPaymentSetting->detokenizeCcNum($user_payment_setting['UserPaymentSetting']['ccToken']);
            if ($tmpCC == "4111111111111111") {
                $test_card = true;
            }
            if ((CRON_ENV == 'dev' || CRON_ENV == 'stage') && $test_card) {
                $auto_charge_card = true;
            }

            $autoSendClientWinnerPpv = false;
            // auto charge here
            // -------------------------------------------------------------------------------
            if (!$restricted_auction && $auto_charge_card) {
                $data_post = array();
                $data_post['userId'] = $data['userId'];
                $data_post['ticketId'] = $ticketId;
                $data_post['paymentProcessorId'] = $this->getProcessorIdBySiteId($data['siteId']);
                $data_post['paymentAmount'] = $data['billingPrice'];
                $data_post['initials'] = 'AUTOCHARGE';
                $data_post['autoCharge'] = 1;
                $data_post['saveUps'] = 0;
                $data_post['zAuthHashKey'] = $this->getAuthKeyHash(
                    $data_post['userId'],
                    $data_post['ticketId'],
                    $data_post['paymentProcessorId'],
                    $data_post['paymentAmount'],
                    $data_post['initials']
                );
                $data_post['userPaymentSettingId'] = $user_payment_setting['UserPaymentSetting']['userPaymentSettingId'];

                $data_post_result = $this->processPaymentTicket(json_encode($data_post));
                if ($data_post_result == 'CHARGE_SUCCESS') {
                    $ppv_settings['ppvNoticeTypeId'] = 18; // Auction Winner Email (PPV)
                    $autoSendClientWinnerPpv = true;
                } else {
                    CakeLog::write(
                        "web_service_tickets_controller",
                        var_export(array("WEB SERVICE TICKETS: ", $data_post_result), 1)
                    );
                    $ppv_settings['ppvNoticeTypeId'] = 19; // Auction Winner Email (Declined / Expired CC)
                }
            }

            // send out winner notifications
            // located in ../vendors/email_msgs in toolbox, not on specific site
            $this->ppv(json_encode($ppv_settings));

            return true;
        } else {
            $this->errorResponse = 1105;
            $this->errorMsg = "Detected re-processing of ticket.";
            return false;
        }
    }

    /**
     * @param $siteId
     * @return int
     */
    private function getProcessorIdBySiteId($siteId)
    {
        $processorId = 1;
        switch($siteId) {
            case 1:
                // Luxury Link / NOVA
                $processorId = 1;
                break;
            case 2:
                // Family Getaway / Paypal
                $processorId = 3;
                break;
        }

        return $processorId;
    }

    private function getAuthKeyHash($userId, $ticketId, $paymentProcessorId, $paymentAmount, $initials)
    {
        $secret = 'L33T_KEY_LL';
        return md5($secret . $userId . $ticketId . $paymentProcessorId . $paymentAmount . $initials);
    }
    public function autoSendFromCheckout($in0)
    {
        // from the frontend checkout, only ticketId comes in.  fill the rest for security
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'AUTO_USER_CHECKOUT';

        // send both the client and winner ppvs
        // -------------------------------------------------------------------------------
        $params['ppvNoticeTypeId'] = 4; // client PPV
        $this->ppv(json_encode($params));

        $params['ppvNoticeTypeId'] = 18; // Auction Winner Email (PPV)
        $this->ppv(json_encode($params));
    }

    public function autoSendPreferredDates($in0)
    {
        // from the frontend my dates request
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'AUTO_USER_DATES';

        $aucPreferDates = $params['dates_json'];
        unset($params['dates_json']);

        // send both the my dates have been received and reservation request
        // -------------------------------------------------------------------------------

        if (isset($params['siteId']) && $params['siteId'] == 2) {
            $params['ppvNoticeTypeId'] = 20; // Your Dates Have Been Received
        } else {
            $params['ppvNoticeTypeId'] = 12; // Your reservation request has been submitted to %%HotelName%%
        }

        $this->ppv(json_encode($params));

        // ppvNoticeTypeId 2 is the new res request with client res xtranet
        $params['ppvNoticeTypeId'] = 2; // Reservation Request

        // if multi-product offer, then send old res request w/o client res xtranet
        if ($this->Ticket->isMultiProductPackage($params['ticketId'])) {
            $params['ppvNoticeTypeId'] = 10; // old res request
        }
        $expirationCriteriaId = $this->Ticket->getExpirationCriteria($params['ticketId']);
        if ($expirationCriteriaId == 5) {
            // this is retail value
            $params['ppvNoticeTypeId'] = 10; // old res request
        }

        // check if preferred dates are two days - if so send availabilty request only
        if (!empty($aucPreferDates)) {
            $arrival_within_2_days = strtotime('+2 DAYS'); // 48 hrs from now
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

    public function autoSendXnetDatesNotAvail($in0)
    {
        // from the XNET - dates are NOT available
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_DATES_NOT_AVAIL';
        $params['ppvNoticeTypeId'] = 14;
        $this->ppv(json_encode($params));
    }

    public function sendResRequestReminder($in0)
    {
        // from the XNET - dates are NOT available
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_RES_REMINDER';
        $params['ppvNoticeTypeId'] = 24;
        $this->ppv(json_encode($params));
    }

    public function sendResRequestReminderCustomer($in0)
    {
        // from the XNET - dates are NOT available
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_RES_REMIND_CUST';
        $params['ppvNoticeTypeId'] = 32;
        $this->ppv(json_encode($params));
    }

    public function autoSendXnetDatesConfirmed($in0)
    {
        // from the XNET - dates are CONFIRMED
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_DATES_CONFIRMED';
        $params['ppvNoticeTypeId'] = 1;
        $this->ppv(json_encode($params));
        $params['ppvNoticeTypeId'] = 23;
        $this->ppv(json_encode($params));
    }

    public function autoSendXnetDatesConfirmedOnlyProperty($in0)
    {
        // from the XNET - dates are CONFIRMED
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_DATES_CONF_PROP';
        $params['ppvNoticeTypeId'] = 23;
        $this->ppv(json_encode($params));
    }

    public function autoSendXnetDatesConfirmedSeasonalPricing($in0)
    {
        // from the XNET - dates are CONFIRMED
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_DATES_CONFIRMED';
        $params['ppvNoticeTypeId'] = 1;
        $ticketId = $params['ticketId'];
        $ticket = $this->Ticket->read(null, $ticketId);
        $ticketData = $ticket['Ticket'];
        switch ($ticketData['siteId']) {
            case 1:
                $siteName = "luxurylink.com";
                break;
            case 2:
                $siteName = "familygetaway.com";
                break;
        }
        $params['override_email_to'] = 'reservations@' . $siteName;
        $this->ppv(json_encode($params));
        $newTicketStatus = 14; //seasonal pricing
        $this->updateTicketStatus($ticketId, $newTicketStatus);

    }

    public function FixedPriceCardCharge($in0)
    {
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

        if (!$ticketData) {
            $this->errorResponse = 2013;
            $this->errorTitle = 'Invalid Data';
            $this->errorMsg = 'Ticket processing was aborted due to receiving invalid ticket data.';
            return false;
        }

        $isChargeSuccess = $this->CardCharge($ticketId);

        if (!$isChargeSuccess && $this->errorResponse) //critical error
        {
            return false;
        } else {
            if (!$isChargeSuccess && !$this->errorResponse) { //charge declined
                $newTicketStatus = 15;
                $this->updateTicketStatus($ticketId, $newTicketStatus);

                $paramEncoded = json_encode($params);
                $this->autoSendXnetCCDeclined($paramEncoded);
            } else {
                //successfully charged
                $paramEncoded = json_encode($params);
                $this->autoSendXnetDatesConfirmed($paramEncoded);
            }
        }

    }

    public function autoSendXnetCCDeclined($in0)
    {
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_CC_DECLINED';
        $params['ppvNoticeTypeId'] = 19;
        $this->ppv(json_encode($params));

    }

    public function CardCharge($ticketId)
    {
        $ticketData = $this->Ticket->query("SELECT * FROM ticket WHERE ticketId = $ticketId LIMIT 1");
        $ticketData = $ticketData[0]['ticket'];
        $userId = $ticketData['userId'];

        // if valid successful charge exists, then return true
        // =====================================================================
        $checkExists = $this->PaymentDetail->query(
            "SELECT * FROM paymentDetail WHERE paymentTypeId = 1 AND ticketId = $ticketId AND userId = $userId"
        );
        if (isset($checkExists[0]['paymentDetail']) && !empty($checkExists[0]['paymentDetail'])) {
            return true;
        }

        // ============================================================
        // ======== [ start process post ] ============================
        // ============================================================


        $gUserPaymentSettingId = $ticketData['userPaymentSettingId'];

        if ($gUserPaymentSettingId) {
            $paymentProcessorId = $this->getProcessorIdBySiteId($ticketData['siteId']);
            $paymentInitials = 'FPCARDCHARGE';
            $data = array(
                'userId' => $userId,
                'ticketId' => $ticketId,
                'paymentProcessorId' => $paymentProcessorId,
                'paymentAmount' => $ticketData['billingPrice'],
                'initials' => $paymentInitials,
                'autoCharge' => 1,
                'saveUps' => 0,
                'zAuthHashKey' => $this->getAuthKeyHash(
                        $userId,
                        $ticketId,
                        $paymentProcessorId,
                        $ticketData['billingPrice'],
                        $paymentInitials
                    ),
                'userPaymentSettingId' => $ticketData['userPaymentSettingId']
            );

            $data_json_encoded = json_encode($data);
            $response = $this->processPaymentTicket($data_json_encoded);

            if (trim($response) == 'CHARGE_SUCCESS') {
                return true;
            } else {
                return false;
            }

        } else {
            $this->errorResponse = 2014;
            $this->errorTitle = 'Invalid PaymentSetting Id';
            $this->errorMsg = 'Ticket does not contain the paymentSettingId';
            return false;
        }


    }

    public function autoSendXnetDateResRequested($in0)
    {
        // from the XNET - dates are requested
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_DATES_REQUESTED';
        $params['ppvNoticeTypeId'] = 2;
        $this->ppv(json_encode($params));
    }

    public function autoSendXnetCancelConfirmation($in0)
    {
        // from the XNET - cancellation confirmation - confirmed
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_CANCEL_CONFIRM';
        $params['ppvNoticeTypeId'] = 30;
        $this->ppv(json_encode($params));
    }

    public function autoSendXnetResCancelled($in0)
    {
        // from the XNET - client receipt for confirmed cancellation
        // -------------------------------------------------------------------------------
        $params = json_decode($in0, true);
        $params['send'] = 1;
        $params['returnString'] = 0;
        $params['manualEmailBody'] = 0;
        $params['initials'] = 'XNET_CANCEL_RECIEPT';
        $params['ppvNoticeTypeId'] = 31;
        $this->ppv(json_encode($params));
    }

    public function numF($str)
    {
        // for commas thousand group separator
        return number_format($str);
    }

    public function ppv_multiple($inData)
    {
        if (!is_array($inData)) {
            return "Data isn't array!";
        }

        foreach ($inData as $r) {
            $return[] = $this->ppv($r);
        }

        return implode("\n", $return);
    }
    /*
     * TODO Refactor some parts of this
     * Thar she blows! It be da bane of scurvy coders everywhere, the dreaded 1000+ line function!!!
     *  In my days, I've seen these monsters like these with over 50 parameters.
     */
    public function ppv($in0)
    {

        $isMystery = false;
        $headerRed = false;
        $hideSalutation = false;
        $resConfirmationNotes = false;

        // Can send in array or JSON string. Useful for using ppv() inside toolbox
        if (!is_array($in0)) {
            $params = json_decode($in0, true);
        } else {
            $params = $in0;
        }
        // TODO THIS METHOD NEEDS SOME MAJOR REVAMP

        // required params for sending and viewing ppvs
        // -------------------------------------------------------------------------------
        $ticketId = isset($params['ticketId']) ? $params['ticketId'] : null;
        $username = isset($params['username']) ? $params['username'] : null;
        $userId = isset($params['userId']) ? $params['userId'] : null;
        $send = isset($params['send']) ? $params['send'] : false;
        $returnString = isset($params['returnString']) ? $params['returnString'] : false;
        $manualEmailBody = isset($params['manualEmailBody']) ? $params['manualEmailBody'] : null;
        $ppvNoticeTypeId = isset($params['ppvNoticeTypeId']) ? $params['ppvNoticeTypeId'] : null;
        $ppvInitials = isset($params['initials']) ? $params['initials'] : null;
        $clientIdParam = isset($params['clientId']) ? $params['clientId'] : false;
        $siteId = isset($params['siteId']) ? $params['siteId'] : false;
        $offerId = isset($params['offerId']) ? $params['offerId'] : false;
        $clientId = isset($params['clientId']) ? $params['clientId'] : false;
        if (!$clientId && $ticketId) {
            $packageId = $this->Ticket->field('packageId', array('Ticket.ticketId' => $ticketId));
            if ($packageId) {
                $clientId = $this->ClientLoaPackageRel->field(
                    'clientId',
                    array('ClientLoaPackageRel.packageId' => $packageId)
                );
            }
        }
        $pgBookingId = isset($params['pgBookingId']) ? $params['pgBookingId'] : null;
        if ($pgBookingId) {
            $bookingDataResult = $this->PgBooking->query("SELECT PgBooking.*, UserPaymentSetting.city, UserPaymentSetting.state, UserPaymentSetting.country, UserPaymentSetting.ccToken, UserPaymentSetting.ccType, PromoCode.promoCode FROM pgBooking PgBooking INNER JOIN userPaymentSetting UserPaymentSetting USING(userPaymentSettingId) LEFT JOIN promoCode PromoCode USING(promoCodeId) WHERE PgBooking.pgBookingId = " . $pgBookingId);
            $bookingData = $bookingDataResult[0];
            $userId = $bookingData['PgBooking']['userId'];
            $clientId = $bookingData['PgBooking']['clientId'];
            $siteId = 1;
            $bookingPaymentResult = $this->PgBooking->query("SELECT * FROM pgPayment PgPayment WHERE pgBookingId  = " . $pgBookingId);
            $bookingPaymentData = array();
            foreach ($bookingPaymentResult as $r) {
                $bookingPaymentData[$r['PgPayment']['paymentTypeId']] = $r['PgPayment'];
            }
        }

        // package id for deal alerts
        if ($ppvNoticeTypeId == 41 || $ppvNoticeTypeId == 42 || $ppvNoticeTypeId == 43) {
            $packageId = isset($params['packageId']) ? $params['packageId'] : null;
        }

        // sender signature (mainly for manual emails sent from toolbox)
        // -------------------------------------------------------------------------------
        $sender_sig = isset($params['sender_sig']) ? $params['sender_sig'] : 0;
        $sender_sig_line = isset($params['sender_sig_line']) ? $params['sender_sig_line'] : '';
        $sender_email = isset($params['sender_email']) ? $params['sender_email'] : '';
        $sender_ext = isset($params['sender_ext']) ? $params['sender_ext'] : '';

        // override the to and cc fields from toolbox manual send
        // -------------------------------------------------------------------------------
        $override_email_to = isset($params['override_email_to']) && !empty($params['override_email_to']) ? $params['override_email_to'] : false;
        $override_email_cc = isset($params['override_email_cc']) && !empty($params['override_email_cc']) ? $params['override_email_cc'] : false;
        $override_email_subject = isset($params['override_email_subject']) && !empty($params['override_email_subject']) ? $params['override_email_subject'] : false;

        //added hb for attachment
        // -------------------------------------------------------------------------------
        $email_attachment = isset($params['emailAttachment']) && !empty($params['emailAttachment']) ? $params['emailAttachment'] : false;
        $email_attachment_type = isset($params['emailAttachmentType']) && !empty($params['emailAttachmentType']) ? $params['emailAttachmentType'] : false;

        // TODO: error checking for params

        if ($ticketId == null && $username == null && !$offerId && !$userId && !isset($params['userEmail']) && $pgBookingId == null) {
            return 'Invalid input';
            exit;
        }

        if ($ticketId) {
            // retrieve data to fill out the email templates
            // -------------------------------------------------------------------------------
            $this->Ticket->recursive = 0;
            $this->Address->recursive = -1;
            $this->ClientLoaPackageRel->recursive = 0;
            $ticket = $this->Ticket->read(null, $ticketId);
            $isForeignCurrencyTicket = $this->Ticket->isForeignCurrencyTicket($ticketId);

            $siteId = $ticket['Ticket']['siteId'];
            $offerId = $ticket['Ticket']['offerId'];
        } elseif ($offerId || $clientId) {
            if (!$siteId) {
                return "Invalid site ID";
                exit;
            }
        }

        $offerSite = Configure::read("OfferSite" . $siteId);

        if ($clientId) {
            $clientData = $this->ClientLoaPackageRel->findByclientid($clientId);
            $clientData = array($clientData);
        }

        if ($offerId || $ticketId) {

            $bidInfo = $this->Bid->getBidStatsForOffer($offerId);
            $q = "select * from $offerSite as LiveOffer where offerId = " . $offerId . " limit 1";
            $liveOfferData = $this->Ticket->query($q);
            $liveOfferData = $liveOfferData[0]['LiveOffer'];
            $packageId = $liveOfferData['packageId'];
            $packageName = strip_tags($liveOfferData['offerName']);

            // Note that $packageIncludes is being set to 'offerIncludes' from offer table, as distinct from
            // having $packageIncludes being set to 'packageIncludes from package table
            $packageIncludes = $liveOfferData['offerIncludes'];

            $legalText = $liveOfferData['termsAndConditions'];
            $validityNote = $liveOfferData['validityDisclaimer'];

            // if validityNote lead in line is not bold, bold it
            if (strstr($validityNote, "This package is valid for ") && substr($validityNote, 0, 3) != '<b>') {
                $validityNote = preg_replace("~This package is valid for [^<]+~is", "<b>$0</b>", $validityNote);
            }

            $addtlDescription = $liveOfferData['additionalDescription'];
            $numGuests = $liveOfferData['numGuests'];
            $roomGrade = $liveOfferData['roomGrade'];
            $packageBlurb = ucfirst($liveOfferData['packageBlurb']);
            $offerEndDate = date('M d Y H:i A', strtotime($liveOfferData['endDate']));
            $maxNumWinners = $liveOfferData['numWinners'];
            $numRooms = $liveOfferData['numRooms'];

            $clientData = $this->ClientLoaPackageRel->findAllBypackageid($liveOfferData['packageId']);
            $isMystery = isset($liveOfferData['isMystery']) && $liveOfferData['isMystery'] ? true : false;

            // When making mystery auctions, the admin will enter generic data into the 'offerIncludes' field
            // in the offer table. However, once the mystery auction is over, the non-bid ppv's should
            // use the specific includes data found in the package table.
            // ticket3631
            $alwaysShowArr = array(1, 12, 14, 18, 19, 26, 30, 32);
            if ($isMystery && in_array($ppvNoticeTypeId, $alwaysShowArr)) {
                $q = "select * from package as Package where packageId = $packageId AND siteId=$siteId ";
                $packageData = $this->Ticket->query($q);
                $packageData = $packageData[0]['Package'];
                $packageIncludes = $packageData['packageIncludes'];
            }

            // ticket 3826
            if (!$clientId && $offerId) {
                $clientId = $liveOfferData['clientId'];
            }
        }

        if ($clientId) {
            $clientImagePath = $this->ImageClient->getFirstImagePath(
                $clientId,
                (isset($isMystery) ? $isMystery : false)
            );
            if (substr($clientImagePath, 0, 1) == '/') {
                $clientImagePath = 'http://photos.luxurylink.us' . $clientImagePath;
            }
        } else {
            $clientImagePath = false;
        }


        $preferDatesHotel = false;
        if ($ticketId) {
            // data arrays
            // -------------------------------------------------------------------------------
            $ticketData = $ticket['Ticket'];
            $packageData = $ticket['Package'];
            $offerData = $ticket['Offer'];
            $userData = $ticket['User'];
            $userAddressData = $this->Address->findByuserid($userData['userId']);
            $userAddressData = $userAddressData['Address'];

            $this->ClientLoaPackageRel->Client->ClientDestinationRel->contain('Destination');
            $offerType = $this->OfferType->find('list');


            $ticketPaymentData = $this->findUserPaymentSettingInfo($ticketData['userPaymentSettingId']);

            $paymentDetail = $this->PaymentDetail->findByticketId($ticketId);

            $paymentDetail = (isset($paymentDetail['PaymentDetail'][0]) ? $paymentDetail['PaymentDetail'][0] : $paymentDetail['PaymentDetail']);

            if ($ticket['Ticket']['useTldCurrency'] == 1) {
                $billingPrice = $ticket['Ticket']['billingPriceTld'];
            } else {
                $billingPrice = $ticket['Ticket']['billingPrice'];
            }

            $promoGcCofData = $this->Ticket->getPromoGcCofData($ticketId, $billingPrice);
            $promoGcCofData['final_price'] = number_format($promoGcCofData['final_price'], 2);

            $promoApplied = (
                isset($promoGcCofData['Promo'])
                && isset($promoGcCofData['Promo']['applied'])
                && $promoGcCofData['Promo']['applied']
            ) ? true : false;

            $cofApplied = (
                isset($promoGcCofData['Cof'])
                && isset($promoGcCofData['Cof']['applied'])
                && $promoGcCofData['Cof']['applied']
            ) ? true : false;

            $giftApplied = (
                isset($promoGcCofData['GiftCert'])
                && isset($promoGcCofData['GiftCert']['applied'])
                && $promoGcCofData['GiftCert']['applied']
            ) ? true : false;

            $preferDatesHotel = $this->ReservationPreferDateFromHotel->find(
                'all',
                array(
                    'conditions' => array('ticketId' => $ticketId)
                ,
                    'order' => array('reservationPreferDateFromHotelId ASC')
                )
            );

        } else {
            if ($username) {
                $this->User->UserSiteExtended->recursive = 0;
                $userId = $this->User->UserSiteExtended->findByusername($username);
            } elseif ($userId) {
                $userId = $this->User->UserSiteExtended->findByuserId($userId);
            } elseif (isset($params['userEmail'])) {
                // Deal alert and other non-user e-mails
                $userData = array('email' => $params['userEmail']);
                if (isset($params['userFirstName'])) {
                    $userData['firstName'] = $params['userFirstName'];
                } else {
                    $userData['firstName'] = $params['userEmail'];
                }
            }

            if (!isset($userData)) {
                if (!empty($userId)) {
                    $userData = array_merge($userId['UserSiteExtended'], $userId['User']);
                } else {
                    return "Invalid User";
                    exit;
                }
            }
        }

        // ********************************************************************************************************
        // ALL VARIABLES ARE SET HERE -- WE DONT HAVE TO CHANGE A MILLION TEMPLATES IF CHANGE IS MADE TO DB FIELD
        // *********************************************************************************************************

        // ********* SITE NAME **********
        switch ($siteId) {
            case 1:
                $siteName = 'Luxury Link';
                $siteDisplay = 'LuxuryLink.com';
                $siteEmail = 'luxurylink.com';
                $siteUrl = 'http://www.luxurylink.com/';
                $siteHeader = '990000';
                $sitePhone = '(888) 297-3299';
                $sitePhoneLocal = '(310) 215-8060';
                $sitePhoneLong = '(888) 297-3299 or +1 (310) 215-8060';
                $siteFax = '(310) 215-8279';
                $headerLogo = 'http://www.luxurylink.com/images/email/LL_logo-V3.jpg';
                $append = "LL";
                $prefixUrl = Configure::read("UrlS.LL");
                $optoutLink = 'http://echo3.bluehornet.com/phase2/survey1/change.htm?cid=mumogm&1362532207';
                if (isset($ticketData)) {
                    $tldId = (isset($ticketData['tldId']) && intval($ticketData['tldId']) > 0) ? $ticketData['tldId'] : 1;
                } elseif ($bookingData) {
                    $tldId = (isset($bookingData['tldId']) && intval($bookingData['tldId']) > 0) ? $bookingData['tldId'] : 1;
                } elseif ($userData) {
                    $tldId = (isset($userData['tldId']) && intval($userData['tldId']) > 0) ? $userData['tldId'] : 1;
                } else {
                    $tldId = 1;
                }
                break;

            case 2:
                $siteName = 'FamilyGetaway.com';
                $siteDisplay = 'FamilyGetaway.com';
                $siteEmail = 'familygetaway.com';
                $siteUrl = 'http://www.familygetaway.com/';
                $siteHeader = 'DE6F0A';
                $sitePhone = '(877) 372-5877';
                $sitePhoneLocal = '(310) 956-3703';
                $sitePhoneLong = '(877) 372-5877 or +1 (310) 956-3703';
                $siteFax = '(800) 440-3820';
                $headerLogo = 'http://www.luxurylink.com/images/family/fglogo_minimal.gif';
                $append = "FG";
                $prefixUrl = Configure::read("UrlS.FG");
                $optoutLink = 'http://echo3.bluehornet.com/phase2/survey1/change.htm?cid=rrsxdv&1362532840';
                $tldId = 1;
                break;

            case 3:
                $siteDisplay = '';
                $siteName = 'Vacationist.com';
                $siteEmail = 'vacationist.com';
                $sitePhone = '(877) 313-6769';
                $sitePhoneLocal = '(310) 956-3704';
                $sitePhoneLong = '(877) 313-6769 or +1 (310) 956-3704';
                $siteFax = '(310) 215-8279';
                $optoutLink = 'http://echo3.bluehornet.com/phase2/survey1/change.htm?cid=tcskep&1362533132';
                $tldId = 1;
                break;
        }

        if ($ppvNoticeTypeId == 41 || $ppvNoticeTypeId == 43) {
            $tldId = isset($params['tldId']) ? $params['tldId'] : 1;
        }

        $lltgServiceBuilder = $this->LltgServiceHelper->getServiceBuilderFromTldId($tldId);
        $lltgComponentService = $this->LltgServiceHelper->getComponentService($lltgServiceBuilder);
        $lltgFormatterService = $this->LltgServiceHelper->getFormatterService($lltgServiceBuilder);
        $lltgTranslationService = $this->LltgServiceHelper->getTranslationService($lltgServiceBuilder);

        $word_packages_upper = $lltgTranslationService->getTranslationforKey('TEXT_PACKAGES_UPPER');
        $word_package_upper = $lltgTranslationService->getTranslationforKey('TEXT_PACKAGE_UPPER');
        $word_packages_lower = $lltgTranslationService->getTranslationforKey('TEXT_PACKAGES_LOWER');
        $word_package_lower = $lltgTranslationService->getTranslationforKey('TEXT_PACKAGE_LOWER');

        $sitePhoneTld = $sitePhone;
        $sitePhoneLocalTld = $sitePhoneLocal;
        if ($tldId == 2) {
            $siteDisplay = 'Luxury Link';
            $prefixUrl = 'https://www.luxurylink.co.uk';
            $siteUrl = 'http://www.luxurylink.co.uk/';
            $sitePhoneTld = $lltgComponentService->getTollfreeNumberFormatted();
            $sitePhoneLocalTld = $lltgFormatterService->formatUSPhoneNumber('1' . preg_replace('/\D/', '', $sitePhoneLocalTld));
            $sitePhoneLong = $sitePhoneTld;
        }

        // Auction facilitator
        $userId = isset($userData['userId']) ? $userData['userId'] : false;
        $userFirstName = isset($userData['firstName']) ? ucwords(strtolower($userData['firstName'])) : false;
        $userLastName = isset($userData['lastName']) ? ucwords(strtolower($userData['lastName'])) : false;
        $emailName = $userFirstName . " " . $userLastName;
        $userEmail = isset($userData['email']) ? $userData['email'] : false;
        $guestEmail = isset($userData['email']) ? $userData['email'] : false;

        $userWorkPhone = isset($userData['workPhone']) ? $userData['workPhone'] : false;
        $userMobilePhone = isset($userData['mobilePhone']) ? $userData['mobilePhone'] : false;
        $userHomePhone = isset($userData['homePhone']) ? $userData['homePhone'] : false;

        $userPhone = $userHomePhone;
        $userPhone = !$userPhone && $userMobilePhone ? $userMobilePhone : $userPhone;
        $userPhone = !$userPhone && $userWorkPhone ? $userWorkPhone : $userPhone;

        $fallPromo2013 = false;
        $blackFridayPromo2013 = false;
        if ($ppvNoticeTypeId == 39 || $ppvNoticeTypeId == 40 || $ppvNoticeTypeId == 41 || $ppvNoticeTypeId == 42) {
            if (time() < strtotime('12/03/2013')) {
                $blackFridayPromo2013 = true;
            }
        }

        $dateNow = date("M d, Y");

		if ($pgBookingId) {
            if ($tldId == 1) {
                $currency = 'USD';
                $currencySymbol = '$';
            } else if ($tldId == 2) {
                $currency = 'GBP';
                $currencySymbol = '&pound;';
            }
		}

        if ($ticketId) {
            $offerId = $offerData['offerId'];
            $packageSubtitle = $packageData['subtitle'];

            $packageId = $ticketData['packageId'];

            $numNights = $ticketData['numNights'];

            $numRooms = $packageData['numRooms'];

            $offerTypeId = $ticketData['offerTypeId'];
            $offerTypeName = str_replace('Standard ', '', $offerType[$offerTypeId]);
            $offerTypeBidder = ($offerTypeId == 1) ? 'Winner' : 'Winning Bidder';
            $isAuction = in_array($offerTypeId, array(1, 2, 6)) ? true : false;
            $isAuction = in_array($ppvNoticeTypeId, array(36)) ? true : $isAuction;

            $billingPrice = ($ticket['Ticket']['useTldCurrency'] == 1) ? $ticketData['billingPriceTld'] : $ticketData['billingPrice'];
            $billingPrice = $this->numF($billingPrice);
            $llFeeAmount = $this->Ticket->getFeeByTicket($ticketId);
            $llFee = $llFeeAmount;
            $originalBillingPriceInDollars = $this->numF($ticketData['billingPrice']);

            if ($tldId == 1 || $ticket['Ticket']['useTldCurrency'] != 1) {
                $currency = 'USD';
                $currencySymbol = '$';
            } else if ($tldId == 2) {
                $currency = 'GBP';
                $currencySymbol = '&pound;';
            }

            $isTaxIncluded = (isset($ticketData['isTaxIncluded'])) ? $ticketData['isTaxIncluded'] : null;

            $foreignPricedPackage = false;
            if (intval($packageData['currencyId']) > 1 && intval($ticketData['offerRetailValueUSD']) > 0 && intval($ticketData['offerRetailValueLocal']) > 0 && (intval($ticketData['offerRetailValueUSD']) != intval($ticketData['offerRetailValueLocal']))) {
            	if (true) {
            		$foreignPricedPackage = array();
            		$foreignPricedPackage['exchangeRate'] = $ticketData['offerRetailValueLocal'] / $ticketData['offerRetailValueUSD'];
            		$foreignPricedPackage['billingPriceLocal'] = $ticketData['billingPrice'] * $foreignPricedPackage['exchangeRate'];
					$currencyCodeData = $this->Ticket->query("SELECT currencyCode FROM currency WHERE currencyId = " . $packageData['currencyId']);
            		$foreignPricedPackage['currencyCode'] = $currencyCodeData[0]['currency']['currencyCode'];
            	}
            }

            //////////////////////////////////////////////////////////////////////////////////////////////////////
            // TODO: figure out how to share this with the customer facing sites
            //
            // Is currently used in LL's my/ directory
            //
            // Can't does this without including php/includes/php/setup.php (launchpad, etc)
            // App::import('Vendor', 'PPvHelper', array('file' => 'appshared' . DS . 'helpers' . DS . 'PpvHelper.php'));
            $checkoutHash = md5($ticketId . $userId . $offerId . 'LL_L33T_KEY');
            $checkoutKey = base64_encode(
                serialize(
                    array('ticketId' => $ticketId, 'userId' => $userId, 'offerId' => $offerId, 'zKey' => $checkoutHash)
                )
            );
            $checkoutLink = $prefixUrl . "/my/my_purchase.php?z=$checkoutKey";
            //
            //////////////////////////////////////////////////////////////////////////////////////////////////////

            $dateRequestLink = $prefixUrl . "/my/my_date_request.php?tid=$ticketId";

            $loaLevelId = isset($clientData[0]['Loa']['loaLevelId']) ? $clientData[0]['Loa']['loaLevelId'] : false;

            $offerTypeArticle = in_array(
                strtolower($offerType[$offerTypeId]{0}),
                array('a', 'e', 'i', 'o', 'u')
            ) ? 'an' : 'a';

            // fixed price variables
            // -------------------------------------------------------------------------------
            $fpRequestType = (isset($wholesale) && $wholesale) ? 'A Wholesale Exclusive' : 'An Exclusive';
            $fpArrival = isset($ticketData['requestArrival']) ? date(
                'M d, Y',
                strtotime($ticketData['requestArrival'])
            ) : 'N/A';
            $fpDeparture = isset($ticketData['requestDeparture']) ? date(
                'M d, Y',
                strtotime($ticketData['requestDeparture'])
            ) : 'N/A';
            $fpArrival2 = isset($ticketData['requestArrival2']) && ($ticketData['requestArrival2'] != '0000-00-00') ? date(
                'M d, Y',
                strtotime($ticketData['requestArrival2'])
            ) : 'N/A';
            $fpDeparture2 = isset($ticketData['requestDeparture2']) && ($ticketData['requestDeparture2'] != '0000-00-00') ? date(
                'M d, Y',
                strtotime($ticketData['requestDeparture2'])
            ) : 'N/A';
            $fpArrivalLast = ($fpArrival2 == "N/A" ? $fpArrival : $fpArrival2);
            $fpDepartureLast = ($fpDeparture2 == "N/A" ? $fpDeparture : $fpDeparture2);

            $fpNumGuests = $ticketData['requestNumGuests'];
            $fpNotes = $ticketData['requestNotes'];

            $offerTypeTxt = (isset($isAuction) && $isAuction) ? 'Auction' : 'Buy Now';

            // auction preferred dates
            // -------------------------------------------------------------------------------
            $aucPreferDates = $this->Ticket->query(
                "SELECT * FROM reservationPreferDate as rpd WHERE ticketId = $ticketId ORDER BY reservationPreferDateTypeId"
            );
            if (!empty($aucPreferDates)) {
                foreach ($aucPreferDates as $aucKey => $aucPreferDateRow) {
                    $aucPreferDates[$aucKey]['rpd']['in'] = date(
                        'M d, Y',
                        strtotime($aucPreferDateRow['rpd']['arrivalDate'])
                    );
                    $aucPreferDates[$aucKey]['rpd']['out'] = date(
                        'M d, Y',
                        strtotime($aucPreferDateRow['rpd']['departureDate'])
                    );
                }
            }

            if (!empty($aucPreferDates)) {
                foreach ($aucPreferDates as $k => $v) {
                    $appendN = $k;
                    if ($k == 0) {
                        $appendN = "";
                    }

                    $appendA = "fpArrival" . $appendN;
                    $appendD = "fpDeparture" . $appendN;
                    $$appendA = ($v['rpd']['in']) ? $v['rpd']['in'] : 'N/A';
                    $$appendD = ($v['rpd']['out']) ? $v['rpd']['out'] : 'N/A';

                    if ($k == (count($aucPreferDates) - 1)) {
                        $fpArrivalLast = $$appendA;
                        $fpDepartureLast = $$appendD;
                    }
                }
            }

            // reservation info
            $resData = $this->Ticket->query(
                "SELECT * FROM reservation WHERE ticketId = $ticketId ORDER BY reservationId DESC LIMIT 1"
            );
            if (!empty($resData)) {
                $resConfNum = $resData[0]['reservation']['reservationConfirmNum'];
                $resArrivalDate = $resData[0]['reservation']['arrivalDate'] ? date(
                    'M d, Y',
                    strtotime($resData[0]['reservation']['arrivalDate'])
                ) : 'N/A';
                $resDepartureDate = $resData[0]['reservation']['departureDate'] ? date(
                    'M d, Y',
                    strtotime($resData[0]['reservation']['departureDate'])
                ) : 'N/A';
                $resConfToCustomer = empty($resData[0]['reservation']['reservationConfirmToCustomer']) ?
                    $resData[0]['reservation']['created']
                    : $resData[0]['reservation']['reservationConfirmToCustomer'];
                $resConfBy = $resData[0]['reservation']['confirmedBy'];
                $resArrDate = $resData[0]['reservation']['arrivalDate'];
                $resDepDate = $resData[0]['reservation']['departureDate'];
                // 07/06/11 - jwoods added
                $resConfirmationNotes = $resData[0]['reservation']['confirmationNotes'];
            }

            // Set reservation date to REQUESTED date. These PPVs are sent when ticket doesn't yet have reservation
            if (in_array($ppvNoticeTypeId, array(24, 2, 10, 28, 11, 54))) {
                $resArrivalDate = $fpArrival;
                $resDepartureDate = $fpDeparture;
            }

            // Calculate cancellation fee. < 15 days from arrival, $100 fee, > 15 days from arrival, $35 fee
            if ($ppvNoticeTypeId == 30) {
                if ($isForeignCurrencyTicket) {
                    $cancelFee = 20;
                } else {
                    $cancelFee = 35;
                }

                if (!empty($resArrDate)) {
                    if (strtotime($resArrDate) - time() < strtotime("+15 days") - time()) {
                        if ($isForeignCurrencyTicket) {
                            $cancelFeel = 65;
                        } else {
                            $cancelFee = 100;
                        }
                    }
                }

                if (!$isForeignCurrencyTicket) {
                    $totalPrice = $this->numF($ticketData['billingPrice'] - $cancelFee);
                    $purchasePrice = $this->numF($ticketData['billingPrice'] + $llFeeAmount);
                } else {
                    $totalPrice = $this->numF($ticketData['billingPriceTld'] - $cancelFee);
                    $purchasePrice = $this->numF($ticketData['billingPriceTld'] + $llFeeAmount);
                }
            } else {
                if (!$isForeignCurrencyTicket) {
                    $totalPrice = $this->numF($ticketData['billingPrice'] + $llFeeAmount);
                } else {
                    $totalPrice = $this->numF($ticketData['billingPriceTld'] + $llFeeAmount);
                }
            }

            // cancellation info
            $ppvNoticeData = $this->Ticket->query(
                "SELECT * FROM ppvNotice WHERE ticketId = $ticketId and ppvNoticeTypeId = 29 ORDER BY created DESC LIMIT 1"
            );
            // you cannot send out cancellation confirmed email unless cancellation request email has been sent
            if (!empty($ppvNoticeData)) {
                $ppvNoticeCreatedDate = date('M d, Y', strtotime($ppvNoticeData[0]['ppvNotice']['created']));
                $canData = $this->Ticket->query(
                    "SELECT * FROM cancellation WHERE ticketId = $ticketId ORDER BY cancellationId DESC LIMIT 1"
                );
                if (!empty($canData)) {
                    $canConfNum = $canData[0]['cancellation']['cancellationNumber'];
                    $canConfBy = $canData[0]['cancellation']['confirmedBy'];
                    $canNote = $canData[0]['cancellation']['cancellationNotes'];
                    $canConfDate = date('M d, Y', strtotime($canData[0]['cancellation']['created']));
                }
            }

            //follow up email sent
            $ppvNoticeData = $this->Ticket->query(
                "SELECT emailSentDatetime FROM ppvNotice WHERE ticketId = $ticketId and ppvNoticeTypeId = 2 ORDER BY created DESC LIMIT 1"
            );
            $emailSentDatetime = (!empty($ppvNoticeData[0]['ppvNotice']['emailSentDatetime'])) ?
                date('M d, Y', strtotime($ppvNoticeData[0]['ppvNotice']['emailSentDatetime'])) : "";

            // cc variables
            // -------------------------------------------------------------------------------
            //last 4 data

            $successPaymentDetail = $this->PaymentDetail->getLastSuccessfullCharge($ticketId);
            if (is_array($successPaymentDetail) && !empty($successPaymentDetail)) {

                $ccFour = $successPaymentDetail['PaymentDetail']['ppCardNumLastFour'];
                $ccType = $successPaymentDetail['PaymentDetail']['ccType'];
                $billDate = $successPaymentDetail['PaymentDetail']['ppResponseDate'];
            }

//			if (is_array($ticketPaymentData) && !empty($ticketPaymentData)) {
//				$ccFour				= substr($ticketPaymentData['UserPaymentSetting']['ccToken'], -4, 4);
//				$ccType				= $ticketPaymentData['UserPaymentSetting']['ccType'];
//				//$billDate			=
//			}

            // guarantee amount
            // -------------------------------------------------------------------------------
            $guarantee = false;

            // 2011-05-03 jwoods - guarantee check
            if ($ticketData['guaranteeAmt'] && is_numeric(
                    $ticketData['guaranteeAmt']
                ) && ($ticketData['guaranteeAmt'] > 0)
            ) {
                if ($ticketData['billingPrice'] < $ticketData['guaranteeAmt']) {
                    $guarantee = $this->numF($ticketData['guaranteeAmt']);
                }
            }

            // guarantee check prior to 2011-05-03 changes
            if (!$guarantee) {
                if ($liveOfferData['reserveAmt'] && is_numeric(
                        $liveOfferData['reserveAmt']
                    ) && ($liveOfferData['reserveAmt'] > 0)
                ) {
                    if ($ticketData['billingPrice'] < $liveOfferData['reserveAmt']) {
                        $guarantee = $this->numF($liveOfferData['reserveAmt']);
                    }
                }

                if ($isMystery) {
                    $guarantee = $this->numF($liveOfferData['reserveAmt']);
                }
            }

            // some unknowns
            // -------------------------------------------------------------------------------
            $wholesale = false;

            // added June 17 -- to allow copy for LL Auc Winner Email and Res Confirmed Email
            if (in_array($ppvNoticeTypeId, array(1, 18))) {
                $primaryDest = $this->Ticket->getTicketDestStyleId($ticketId);
            }

            // check if already sent out a reservation request
            if (in_array($ppvNoticeTypeId, array(2, 10))) {
                $res_request = $this->Ticket->query(
                    "SELECT COUNT(*) AS count FROM ppvNotice where ticketId = {$ticketId} AND ppvNoticeTypeId IN (2,10);"
                );
                $res_request_count = $res_request[0][0]['count'];
            }
        } //End IF for $ticketId

        // this needs to run outside of "if ($ticketId)" because bids (36) do not have tickets yet
        if (!isset($isAuction)) {
            $isAuction = false;
        }
        $isAuction = in_array($ppvNoticeTypeId, array(36)) ? true : $isAuction;

        if (!empty($clientData)) {
            // fetch client contacts
            // -------------------------------------------------------------------------------
            $clients = array();
            $multi_client_map_override = false;
            foreach ($clientData as $k => $v) {
                $tmp = $v['Client'];
                if ($clientIdParam && ($clientIdParam == $tmp['clientId'])) {
                    $multi_client_map_override = $k;
                }
                if (!empty($v['Client']['parentClientId']) && is_numeric(
                        $v['Client']['parentClientId']
                    ) && ($v['Client']['parentClientId'] > 0) && ($v['Client']['clientId'] != $v['Client']['parentClientId'])
                ) {
                    $add_parent_client_sql = "OR clientId = " . $v['Client']['parentClientId'];
                } else {
                    $add_parent_client_sql = '';
                }
                $tmp_result = $this->Ticket->query(
                    "SELECT * FROM clientContact WHERE clientContactTypeId in (1,3) AND (clientId = " . $v['Client']['clientId'] . " $add_parent_client_sql) ORDER BY clientContactTypeId, primaryContact DESC"
                );
                $contact_cc_string = array();
                $contact_to_string = array();
                foreach ($tmp_result as $a => $b) {
                    $contacts = array();
                    $contacts['ppv_name'] = $b['clientContact']['name'];
                    $contacts['ppv_title'] = $b['clientContact']['businessTitle'];
                    $contacts['ppv_email_address'] = $b['clientContact']['emailAddress'];
                    $contacts['ppv_phone'] = $b['clientContact']['phone'];
                    $contacts['ppv_fax'] = $b['clientContact']['fax'];
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

                // Exception for Ritz Carlton -- See Zoe
                if ($clientId == 11627 && in_array($ppvNoticeTypeId, array(40, 41))) {
                    $tmp['contact_to_string'] = '';
                    $tmp['contact_cc_string'] = '';
                }

                $tmp['percentOfRevenue'] = $v['ClientLoaPackageRel']['percentOfRevenue'];
                $clients[$k] = $tmp;
            }

            $isMultiClientPackage = (count($clients) > 1) ? true : false;

            // Multi-client stuff is a mess. This is a partial cleanup.
            // rvella

            App::import(
                "Vendor",
                "UtilityHelper",
                array('file' => "appshared" . DS . "helpers" . DS . "UtilityHelper.php")
            );

            foreach ($clients as $client_index => $row) {
                // $clients[$client_index]['name'] = UtilityHelper::checkUtf8(
                //     $clients[$client_index]['name']
                // ) ? utf8_encode($clients[$client_index]['name']) : $clients[$client_index]['name'];
                // if (isset($clients[$client_index]['nameNormalized']) && $clients[$client_index]['nameNormalized'] != '') {
                //     $clients[$client_index]['name'] = $clients[$client_index]['nameNormalized'];
                // }
                $clients[$client_index]['name'] = UtilityHelper::normalize($clients[$client_index]['name']);

                $clients[$client_index]['estaraPhoneLocal'] = $clients[$client_index]['estaraPhoneLocal'] == null ? $clients[$client_index]['phone1'] : $clients[$client_index]['estaraPhoneLocal'];
                $clients[$client_index]['estaraPhoneIntl'] = $clients[$client_index]['estaraPhoneIntl'] == null ? $clients[$client_index]['phone2'] : $clients[$client_index]['estaraPhoneIntl'];

                // Format phone numbers if possible
                $clients[$client_index]['estaraPhoneLocal'] = UtilityHelper::cleanUSD(
                    $clients[$client_index]['estaraPhoneLocal'],
                    6
                );
                $clients[$client_index]['estaraPhoneIntl'] = UtilityHelper::cleanUSD(
                    $clients[$client_index]['estaraPhoneIntl'],
                    6
                );

                // Check if billingPrice is set, else set it to 0
                $ticketData['billingPrice'] = (isset($ticketData['billingPrice'])) ? $ticketData['billingPrice'] : 0;

                $clients[$client_index]['clientAdjustedPrice'] = $this->numF(
                    ($clients[$client_index]['percentOfRevenue'] / 100) * $ticketData['billingPrice']
                );

                if ($isMystery && ($ppvNoticeTypeId != 18)) {
                    $clients[$client_index]['pdpUrl'] = $siteUrl . "luxury-hotels/mystery-hotel?isMystery=1&oid=" . $offerId;
                } else {
                    $clients[$client_index]['pdpUrl'] = $siteUrl . "luxury-hotels/" . $clients[$client_index]['seoName'] . "?clid=" . $row['clientId'] . "&pkid=" . $packageId;
                }

                $clients[$client_index]['destData'] = $this->ClientLoaPackageRel->Client->ClientDestinationRel->findByclientId(
                    $row['clientId'],
                    array(),
                    "parentId DESC, clientDestinationRelId DESC"
                );
                $clients[$client_index]['themeData'] = $this->ClientThemeRel->find(
                    'all',
                    Array('conditions' => Array('ClientThemeRel.clientId' => $row['clientId']))
                );

                $clients[$client_index]['contact_to_string_trimmed'] = $clients[$client_index]['contact_to_string'];

                if (($pos = strpos($clients[$client_index]['contact_to_string'], ",")) != 0) {
                    // Causing issues when clients have multiple primary RES contacts
                    //$clients[$client_index]['contact_to_string'] = substr($clients[$client_index]['contact_to_string'],0,$pos);

                    $clients[$client_index]['contact_to_string_trimmed'] = substr(
                        $clients[$client_index]['contact_to_string'],
                        0,
                        $pos
                    );
                }
            }

            $client_index = ($multi_client_map_override !== false) ? $multi_client_map_override : 0;

            $clientId = $clients[$client_index]['clientId'];
            $parentClientId = $clients[$client_index]['parentClientId'];
            $clientNameP = $clients[$client_index]['name'];

            $clientName = $clients[$client_index]['contacts'][0]['ppv_name'];
            $oldProductId = $clients[$client_index]['oldProductId'];
            $locationDisplay = UtilityHelper::normalize($clients[$client_index]['locationDisplay']);
            // if (isset($clients[$client_index]['locationNormalized']) && $clients[$client_index]['locationNormalized'] != '') {
            //     $locationDisplay = $clients[$client_index]['locationNormalized'];
            // }

            $clientPrimaryEmail = $clients[$client_index]['contact_to_string'];
            $clientCcEmail = $clients[$client_index]['contact_cc_string'];
            $clientAdjustedPrice = $clients[$client_index]['clientAdjustedPrice'];
            $clientPhone = $clients[$client_index]['estaraPhoneLocal'];
            $clientPhoneIntl = $clients[$client_index]['estaraPhoneIntl'];

            $pdpUrl = $clients[$client_index]['pdpUrl'];
        }

        // Click tracking for templates
        $emailFrom = "$siteDisplay <no-reply@$siteEmail>";
        $emailReplyTo = "no-reply@$siteEmail";

        // fetch template with the vars above
        // -------------------------------------------------------------------------------
        ob_start();

        $specialException = false;
        $clientPpv = false;
        $internalPpv = false;
        $pleaseRespond = false;

        $offerTypeTxt_inEmail = false;

        // "Please respond" header
        switch ($ppvNoticeTypeId) {
            case 2:
            case 10:
            case 24:
            case 27:
            case 29:
            case 33:
                $pleaseRespond = true;
        }

        // Offer Type Text header
        switch ($ppvNoticeTypeId) {
            case 2:
            case 10:
            case 23:
            case 24:
            case 27:
            case 28:
            case 54:
            case 29:
            case 33:
                $offerTypeTxt_inEmail = $offerTypeTxt;
        }


        // Client PPVs
        switch ($ppvNoticeTypeId) {
            case 10:
            case 11:
            case 2:
            case 4:
            case 23:
            case 24:
            case 25:
            case 27:
            case 28:
            case 54:
            case 29:
            case 31:
            case 33:
                $clientPpv = true;
                if ($ppvNoticeTypeId == 29) {
                    $extranet_link = $this->getExtranetCancellationLink($ticketId, $siteId);
                } else {
                    $extranet_link = $this->getExtranetLink($ticketId, $siteId);
                }
                if ($isAuction) {
                    $emailFrom = $emailReplyTo = "reservationrequests@$siteEmail";
                } else {
                    $emailFrom = $emailReplyTo = "reservations@$siteEmail";
                }

                $emailFrom = $siteDisplay . " <" . $emailFrom . ">";
                $userEmail = $clientPrimaryEmail;
                if ($ppvNoticeTypeId != 11) {
                    $emailCc = $clientCcEmail;
                }

                break;
        }

        // "Confirm Reservation" button and other buttons...
        // $emailSubject has not be defined as this point, not sure why it is appended to imgHref,
        // so check/init it
        if (!isset($emailSubject)) {
            $emailSubject = '';
        }
        $imgHref = "mailto:" . $emailReplyTo . "?Subject=Ticket%20" . $ticketId . "%20-%20" . $emailSubject;

        switch ($ppvNoticeTypeId) {
            case 2:
            case 24:
            case 33:
                $imgHref = $extranet_link;
                $imgSrc = "confirm_reservation.gif";
                break;
            case 27:
            case 28:
                $imgHref = $extranet_link;
                $imgSrc = "confirm_reservation.gif";
                break;
            case 54:
                $imgHref = $extranet_link;
                $imgSrc = "confirm_reservation.gif";
                break;
            case 29:
                $imgHref = $extranet_link;
                $imgSrc = "confirm_cancellation.gif";
                break;
            case 10:
                $imgSrc = "reply_with_availability.gif";
                break;
        }

        // Removes promo info for package / offer for clients
        if ($clientPpv) {
            $this->PackageIncludes->removePromoInfo($liveOfferData, 'offer');
            $packageIncludes = $liveOfferData['offerIncludes'];
        }

        switch ($ppvNoticeTypeId) {
            case 1:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/conf_ppv.html');
                } else {
                    $templateFile = "1_reservation_confirmation";
                    if ($isAuction) {
                        $specialException = true;
                    }
                }

                $templateTitle = "Your reservation is confirmed";
                $emailSubject = "Your $siteName Reservation is Confirmed - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <resconfirm@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "resconfirm@$siteEmail" : "reservations@$siteEmail";
                break;
            case 2:
                if ($siteId == 2) {
                    // send out res request
                    include('../vendors/email_msgs/notifications/2_reservation_request.html');
                    $emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $emailName";
                } else {
                    $templateFile = "2_reservation_request_new";
                    $templateTitle = "Luxury Link Booking Request";
                    $emailSubject = "Booking request - Please Confirm";
                }

                if (isset($res_request_count) && $res_request_count > 0) {
                    $emailSubject = 'NEW DATES REQUESTED - ' . $emailSubject;
                }

                break;
            case 4:
                include('../vendors/email_msgs/ppv/client_ppv.html');
                $emailSubject = "$siteName Auction Winner Notification - $emailName";
                $emailReplyTo = "auctions@$siteEmail";

                $emailFrom = $siteDisplay . " <" . $emailReplyTo . ">";
                break;
            case 5:
                include('../vendors/email_msgs/notifications/winner_notification.html');
                $emailSubject = "$siteName Auction Winner - $clientNameP";
                $emailFrom = "$siteDisplay <auction@$siteEmail>";
                $emailReplyTo = "auction@$siteEmail";
                break;
            case 9:
                include('../vendors/email_msgs/fixed_price/msg_fixedprice.html');
                $emailSubject = "$siteName - Your Request Has Been Received";
                $emailFrom = "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = "exclusives@$siteEmail";
                break;
            case 10:
                if ($siteId == 1) {
                    $templateFile = '10_confirm_availability';
                    $templateTitle = "Luxury Link Availability Request Only";
                    $emailSubject = "Booking Request - Please Confirm Availability Only";
                } else {
                    include('../vendors/email_msgs/fixed_price/msg_client_fixedprice.html');
                }

                if (isset($res_request_count) && $res_request_count > 0) {
                    $emailSubject = 'NEW DATES REQUESTED - ' . $emailSubject;
                }

                $emailFrom = "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = "exclusives@$siteEmail";

                if ($isAuction && $siteId == 1) {
                    $emailReplyTo = "reservationrequests@$siteEmail";
                }

                if ($this->Ticket->isMultiProductPackage($ticketId)) {
                    $emailFrom = "$siteDisplay <resrequest@$siteEmail>";
                    $emailReplyTo = "resrequest@$siteEmail";
                }

                break;
            case 11:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/fixed_price/msg_internal_fixedprice.html');
                } else {
                    $internalPpv = true;
                    $templateFile = "11_fp_internal_exclusive";
                    $templateTitle = "Fixed Price Booking Requested";
                }

                $emailSubject = "A Fixed Price Request has Been Made";
                $emailFrom = "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = "exclusives@$siteEmail";
                $userEmail = "exclusives@$siteEmail";
                break;
            case 12:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/notification_acknowledgement.html');
                } else {
                    $templateFile = '12_reservation_ack';
                }

                $templateTitle = "Your reservation request has been submitted to $clientNameP.  Please allow 1-2 business days for a reply.";
                $emailSubject = "Your $siteName Travel Booking - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            case 13:
                include('../vendors/email_msgs/fixed_price/notification_dates_available.html');
                $emailSubject = "Your $siteName Travel Booking - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            case 14:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/notification_dates_not_available.html');
                } else {
                    $templateFile = "14_dates_not_available";
                }

                $templateTitle = "Your requested dates are unavailable";
                $emailSubject = "Your requested dates are unavailable - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            case 15:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/chase_money_notification.html');
                    $emailSubject = "$siteName Auction Winner - $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";

                    break;
                }
            case 16:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/first_offense_flake.html');
                    $emailSubject = "$siteName Auction Winner - $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";
                    break;
                }
            case 19:
                if ($siteId == 2 && $ppvNoticeTypeId == 19) {
                    include('../vendors/email_msgs/notifications/old/19_auction_winner_declined_expired.html');
                } else {
                    $headerRed = true;
                    $templateFile = "19_auction_winner_declined_expired";
                }

                $templateTitle = "ACTION REQUIRED: Transaction Incomplete";
                $emailSubject = "Please Respond - Transaction Incomplete";

                if ($ppvNoticeTypeId == 16) {
                    $templateTitle .= " - Second Attempt";
                    $emailSubject .= " - Second Attempt";
                }

                $emailReplyTo = "auction@$siteEmail";
                $emailFrom = $siteDisplay . " <" . $emailReplyTo . ">";

                break;
            case 17:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/second_offense_flake.html');
                } else {
                    $templateFile = "17_second_offense_flake";
                }

                $emailSubject = $templateTitle = "Your $siteName bidding privileges";
                $emailReplyTo = "auction@$siteEmail";
                $emailFrom = $siteDisplay . " <" . $emailReplyTo . ">";

                break;
            case 18:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/18_auction_winner_ppv.html');
                } else {
                    $templateFile = '18_auction_winner_ppv';
                }

                if ($ticket['Package']['isDNGPackage'] == 1) {
                    $templateTitle = "Congratulations on your purchase";
                    $emailSubject = "$siteName Purchase Receipt - $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";
                } else {
                    $templateTitle = "Congratulations - You Won";
                    $emailSubject = "$siteName Auction Winner Receipt - $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";
                }

                break;
            case 20:
                include('../vendors/email_msgs/notifications/20_auction_your_dates_received.html');
                $emailSubject = "Your $siteName Request has been Received - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            case 23:
                // send out res confirmation to client also as copy
                // include('../vendors/email_msgs/ppv/23_conf_copy_client.php');
                $templateFile = "23_reservation_confirmation_copy_client";
                $templateTitle = "Luxury Link Booking Confirmation";
                $emailSubject = "$siteName Booking Confirmed for $emailName - $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auctionresreq@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auctionresreq@$siteEmail" : "reservations@$siteEmail";
                $userEmail = $clientPrimaryEmail;
                // ticket 3032
                if (stripos($userEmail, 'phg@luxurylink.com') !== false) {
                    $userEmail = str_ireplace('phg@luxurylink.com', 'phgcfm@luxurylink.com', $userEmail);
                }
                break;
            case 24:
                if ($siteId == 1) {
                    $templateFile = "24_reservation_request_followup_new";
                    $templateTitle = "Immediate Attention Required: Luxury Link Booking Request";
                    $emailSubject = "Booking Request - Immediate Response Required - $emailName";
                } else {
                    // send out res request
                    include('../vendors/email_msgs/notifications/24_reservation_request_followup.html');
                    $emailSubject = "Booking Request - Immediate Response Required - $emailName";
                }

                break;
            case 25:
                // send out res request w/o xnet
                include('../vendors/email_msgs/notifications/25_res_request_no_xnet.html');
                $emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $emailName";
                break;
            case 26:
                // general customer template
                $templateFile = "26_general_customer_template";

                $emailFrom = ($isAuction) ? "$siteDisplay <resconfirm@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "resconfirm@$siteEmail" : "reservations@$siteEmail";

                $templateTitle = "[enter header here]";
                $emailSubject = "Regarding your Luxury Link purchase";

                break;
            case 27:
                if ($siteId == 1) {
                    $templateTitle = "[enter header here]";
                    $templateFile = "27_general_client_template_new";
                    $emailSubject = "Booking Request - Please Confirm - $emailName";
                } else {
                    // general client template
                    include('../vendors/email_msgs/notifications/27_general_client_template.html');
                    $emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $emailName";
                }

                break;
            case 28:
                if ($siteId == 1) {
                    $templateFile = "28_general_res_request_template_new";
                    $templateTitle = "Luxury Link Booking Request";
                    $emailSubject = "Booking Request - Please Confirm - $emailName";
                } else {
                    // general res request template
                    include('../vendors/email_msgs/notifications/28_general_res_request_template.html');
                    $emailSubject = "Please Confirm This $siteName Booking - $offerTypeTxt - ACTION REQUIRED - $emailName";
                }

                break;
            case 54:
                $templateFile = "54_general_res_request_template_new";
                $templateTitle = "Luxury Link Booking Request";
                $emailSubject = "Booking Request - Please Confirm - $emailName";

                break;
            case 29:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/29_reservation_cancel_request.html');
                    $emailSubject = "$siteName Cancellation Request - ACTION REQUIRED - $emailName";
                } else {
                    $emailSubject = "Reservation Cancellation - Please Confirm - $emailName";
                    $templateFile = "29_reservation_cancel_request_new";
                    $templateTitle = "Luxury Link Booking Cancellation";
                }

                break;
            case 30:
                // send out res cancellation confirmation
                $templateFile = "30_reservation_cancel_confirmation";
                $templateTitle = "Your reservation has been cancelled";
                $emailSubject = "Your reservation has been cancelled";
                $emailFrom = ($isAuction) ? "$siteDisplay <resrequests@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";

                if ($isAuction) {
                    $specialException = true;
                }

                break;
            case 31:
                // send out res cancellation confirmation
                $emailFrom = ($isAuction) ? "$siteDisplay <resrequests@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
                $userEmail = $clientPrimaryEmail;

                if ($siteId == 1) {
                    $templateFile = "31_reservation_cancel_receipt";
                    $templateTitle = "Luxury Link booking cancellation receipt";
                    $emailSubject = "Luxury Link Cancellation Confirmation";
                } else {
                    include('../vendors/email_msgs/ppv/cancel_ppv.html');
                    $emailSubject = "Your $siteName Booking was Cancelled. - $clientNameP";
                }
                break;
            case 32:
                include('../vendors/email_msgs/notifications/32_reservation_request_followup_customer.html');
                $emailSubject = "Your Pending Reservation";
                $emailFrom = ($isAuction) ? "$siteDisplay <resrequests@$siteEmail>" : "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "resrequests@$siteEmail" : "reservations@$siteEmail";
                break;
            case 33:
                //include('../vendors/email_msgs/notifications/33_change_dates_request_template.html');
                $templateFile = "33_change_dates_request_template";

                if ($siteId == 1) {
                    $emailSubject = "Luxury Link Booking Request - Change of Dates requested";
                    $templateTitle = "Immediate Attention Required: Change of Dates";
                } else {
                    $emailSubject = "Your $siteName Request has been Received - $clientNameP";
                }

                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            // New logic depends on these -- keeping for now
            case 34:
                $templateFile = "34_forgot_password";
                $emailSubject = $templateTitle = "Your $siteName Password";
                break;
            case 35:
                $templateFile = "35_forgot_username";
                $emailSubject = $templateTitle = "Your $siteName Username";
                break;
            case 36:
                $templateFile = "36_highest_bidder";
                $templateTitle = "You are the highest bidder";
                $emailSubject = "$siteName: Your bid has been received.";

                if (!$isMystery) {
                    $emailSubject .= " - $clientNameP";
                }

                break;
            case 37:
                // Note: This template isn't where it should be, to edit it see
                // app/vendors/shells/templates/post_trip_email.ctp
                $ppvNoticeTypeId = 35; //TODO REMOVE THIS LINE ONCE IT WORKS RIGHT WITH SILVERPOP
                $templateFile = "37_post_trip_email";
                $emailSubject = $templateTitle = "Rate your $siteName experience";
                break;
            case 38:
                $templateFile = "38_auction_watch_ending";
                $templateTitle = "The auction you are watching ends soon";
                $emailSubject = $siteName . ": " . $templateTitle;
                break;
            case 39:
                $templateFile = "39_auction_outbid";
                $templateTitle = "You have been outbid, please bid again.";
                $emailSubject = $siteName . ": " . $templateTitle;
                break;
            case 40:
                $templateFile = "40_leadgen_favorite";
                $emailSubject = $templateTitle = $clientNameP . " has a new vacation experience";
                break;
            case 41:

                $alertOfferTable = ($siteId == 2) ? 'offerFamily' : 'offerLuxuryLink';
                $q = "SELECT packageId, MAX(offerName) AS offerName, DATE_FORMAT(MIN(validityStart), '%M %e, %Y') AS validityStart, DATE_FORMAT(MAX(validityEnd), '%M %e, %Y') AS validityEnd from $alertOfferTable WHERE packageId = $packageId AND isClosed = 0 AND startDate < NOW() AND endDate > NOW() GROUP BY packageId";
                $alertPackage = $this->Ticket->query($q);
                $alertPackageData = $alertPackage[0][0];

                $templateFile = "41_leadgen_alert";
                $emailSubject = $clientNameP . " is back on " . $siteName;
                $templateTitle = $clientNameP . " has a new vacation experience";
                $hideSalutation = true;
                break;
            case 42:
                $emailFrom = "$siteDisplay <customerservice@luxurylink.com>";
                $isAuction = 1;
                $additionalClients = (isset($params['acAdditionalClients'])) ? $params['acAdditionalClients'] : array();
                include('../vendors/email_msgs/notifications/42_43_abandoned_cart.html');
                $emailSubject = "Questions with your " . $clientNameP . " Order?";
                break;
            case 43:
                $emailFrom = "$siteDisplay <customerservice@luxurylink.com>";
                $isAuction = 0;
                $additionalClients = (isset($params['acAdditionalClients'])) ? $params['acAdditionalClients'] : array();
                include('../vendors/email_msgs/notifications/42_43_abandoned_cart.html');
                $emailSubject = "Questions with your " . $clientNameP . " Order?";
                break;
            case 44:
                $vcomSaleInfo = $params['vcomSaleInfo'];
                $vcomSaleInfo['tracking'] = 'utm_source=vac&utm_medium=xa&utm_campaign=abandon_vcom';
                $vcomSaleInfo['pdpLink'] = 'http://www.vacationist.com/visitors/hotels/id/' . $vcomSaleInfo['clientId'] . '/visitor/' . $params['vcomUserHash'] . '/?' . $vcomSaleInfo['tracking'];
                $emailSubject = "Your " . $vcomSaleInfo['name'] . " Order";
                include('../vendors/email_msgs/notifications/44_abandoned_cart_vcom.html');
                break;
            case 45:
                $rafAmount = $params['rafAmount'];
                $emailSubject = "Your account has been credited";
                if ($params['siteId'] == 1) {
                    include('../vendors/email_msgs/notifications/45_raf_paid.html');
                } else {
                    if ($params['ppvVersion'] == 'old') {
                        $name = $params['userFirstName'];
                        include('../vendors/email_msgs/notifications/46_raf_credited.old.html');
                    } else {
                        include('../vendors/email_msgs/notifications/47_raf_credited.html');
                    }
                }
                break;
            case 48:
                $emailBcc = 'EventRegistry@luxurylink.com';
                $eventRegistryUrl = $params['eventRegistryUrl'];
                $emailSubject = "Your Luxury Link Honeymoon Registry has been Created";
                include('../vendors/email_msgs/notifications/48_registry_created_honeymoon.html');
                break;
            case 49:
                $eventRegistryUrl = $params['eventRegistryUrl'];
                $eventRegistryName = $params['eventRegistryName'];
                $eventRegistryFullName = $params['eventRegistryFullName'];
                $eventRegistryMessage = $params['eventRegistryMessage'];
                $emailSubject = "A Message from " . $eventRegistryFullName;
                include('../vendors/email_msgs/notifications/49_registry_share_honeymoon.html');
                break;
            case 50:
                $giftMessage = $params['giftMessage'];
                $giftFromName = $params['giftFromName'];
                $giftFromFullName = $params['giftFromFullName'];
                $emailSubject = $giftFromFullName . " has Sent You a Gift";
                include('../vendors/email_msgs/notifications/50_registry_congratulations_honeymoon.html');
                break;
            case 51:
                $emailBcc = 'EventRegistry@luxurylink.com';
                $templateFile = "51_registry_gift_receipt";
                $emailSubject = "Luxury Link Gift Receipt";
                $templateTitle = "Thank You for Your Luxury Link Honeymoon Registry Contribution";
                $ticketId = $params['transactionNumber'];
                $eventRegistryName = $params['eventRegistryName'];
                $giftAmount = $params['giftAmount'];
                $ccFour = $params['ccFour'];
                break;
            case 52:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/52_no_dates_reminder.html');
                } else {
                    $templateFile = '52_no_dates_reminder';
                }

                if ($ticket['Package']['isDNGPackage'] == 1) {
                    $templateTitle = "Please Request Your Preferred Travel Dates";
                    $emailSubject = "$siteName Purchase Receipt Reminder, Please Request Your Preferred Travel Dates for $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";
                } else {
                    $templateTitle = "Please Request Your Preferred Travel Dates";
                    $emailSubject = "$siteName Auction Winner Reminder, Please Request Your Preferred Travel Dates for $clientNameP";
                    $emailFrom = "$siteDisplay <auction@$siteEmail>";
                    $emailReplyTo = "auction@$siteEmail";
                }
                break;
            case 53:
                if ($siteId == 2) {
                    include('../vendors/email_msgs/notifications/old/53_new_dates_needed_reminder.html');
                } else {
                    $templateFile = "53_new_dates_needed_reminder";
                }

                $templateTitle = "Please Request Alternate Travel Dates";
                $emailSubject = "Please Submit Alternate Travel Dates for $clientNameP";
                $emailFrom = ($isAuction) ? "$siteDisplay <auction@$siteEmail>" : "$siteDisplay <exclusives@$siteEmail>";
                $emailReplyTo = ($isAuction) ? "auction@$siteEmail" : "exclusives@$siteEmail";
                break;
            case 54:
                $userStrandsHash = isset($params['strandsHash']) ? $params['strandsHash'] : null;
                $emailFrom = "$siteDisplay <customerservice@luxurylink.com>";
                //  $additionalClients = (isset($params['acAdditionalClients'])) ? $params['acAdditionalClients'] : array();
                include('../vendors/email_msgs/notifications/54_abandoned_pdp.html');
                $emailSubject = "The Hunt Is Over for that Perfect Vacation in ".$locationDisplay;
                break;
            case 60:
                include('../vendors/email_msgs/notifications/60_pegasus_confirmation.html');
                $emailFrom = "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = "reservations@$siteEmail";
                $emailSubject = "Your Luxury Link Travel Booking - $clientNameP";
                break;
            case 61:
                $templateFile = "61_pegasus_cancel_confirmation";
                $templateTitle = "Your Luxury Link Travel Booking Has Been Cancelled";
                $emailSubject = "Your Luxury Link Travel Booking Has Been Cancelled";
                $emailFrom = "$siteDisplay <reservations@$siteEmail>";
                $emailReplyTo = "reservations@$siteEmail";
                break;

            default:
                break;
        }

        // Turns mystery option off for winner e-mails to display correct package info
        if (in_array($ppvNoticeTypeId, array(5, 18, 19)) && $isMystery) {
            $isMystery = false;
            $emailSubject = "$siteName Mystery Auction Winner";
        }

        if (isset($templateFile) && $templateFile) {
            if (($template = $this->newEmailTemplate($templateFile, $append, $specialException)) !== false) {
                $rand = rand(100, 1000);
                $file = "/tmp/template-" . $rand;

                file_put_contents($file, $template);
                include($file);
                unlink($file);

                $emailBody = ob_get_clean();
                $emailBody = $this->utmLinks($emailBody, $ppvNoticeTypeId, $append);
            } else {
                CakeLog::write("web_service_tickets_controller", "INVALID TEMPLATE");
                return false;
                exit;
            }
        } else {
            $emailBody = ob_get_clean();
            $ppvNoticeTypeIdArr = array(42, 43, 45, 48, 49, 50);
            if (in_array($ppvNoticeTypeId, $ppvNoticeTypeIdArr)) {
                $emailBody = $this->utmLinks($emailBody, $ppvNoticeTypeId, $append);
            }
        }

        // Returns editable subject part for ppv_notices_controller
        if (isset($params['returnSubject'])) {
            return $emailSubject;
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

            $emailCc = isset($emailCc) ? $emailCc : false;
            $emailCc = trim($override_email_cc) != false ? $override_email_cc : $emailCc;
            $emailBcc = isset($emailBcc) ? $emailBcc : false;

            if (trim($override_email_subject)) {
                $emailSubject = $override_email_subject;
            }

            //$str=$userEmail.$emailFrom.$emailCc.$emailBcc.$emailReplyTo.$emailSubject.$emailBody.$ticketId.$ppvNoticeTypeId.$ppvInitials;
            //$this->Ticket->logIt('email_data');
            //$this->Ticket->logIt($str);

            $this->sendPpvEmail(
                $userEmail,
                $emailFrom,
                $emailCc,
                $emailBcc,
                $emailReplyTo,
                $emailSubject,
                $emailBody,
                $ticketId,
                $ppvNoticeTypeId,
                $ppvInitials,
                false,
                $pgBookingId
            );

            // AUTO SECTION FOR MULTI CLIENT PPV for multi-client packages send client emails [CLIENT PPV]
            // -------------------------------------------------------------------------------
            $count_clients = (isset($clients)) ? count($clients) : 0;
            if ((in_array($ppvNoticeTypeId, array(2, 4, 10))) && (!$manualEmailBody) && ($count_clients > 1)) {
                for ($i = 1; $i < $count_clients; $i++) {
                    $clientId = $clients[$i]['clientId'];
                    $clientNameP = $clients[$i]['name'];
                    $clientName = $clients[$i]['contacts'][0]['ppv_name'];
                    $oldProductId = $clients[$i]['oldProductId'];
                    $locationDisplay = $clients[$i]['locationDisplay'];
                    $clientPrimaryEmail = $clients[$i]['contact_to_string'];
                    $clientCcEmail = $clients[$i]['contact_cc_string'];
                    $clientAdjustedPrice = $this->numF(
                        ($clients[$i]['percentOfRevenue'] / 100) * $ticketData['billingPrice']
                    );
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
                    $this->sendPpvEmail(
                        $clientPrimaryEmail,
                        $emailFrom,
                        $clientCcEmail,
                        $emailBcc,
                        $emailReplyTo,
                        $emailSubject,
                        $emailBody,
                        $ticketId,
                        $ppvNoticeTypeId
                    );
                }
            }
        }

        // return the string for toolbox ppvNotice add screen (manual edit and send)
        // -------------------------------------------------------------------------------
        if ($returnString) {
            return $emailBody;
        } else {
            return "SUCCESS";
        }
    }

    private function newEmailTemplate($templateFile, $append = "LL", $specialException = false)
    {
        // Add UTM links
        $template = file_get_contents("../vendors/email_msgs/includes/header_" . $append . ".html");
        $template .= file_get_contents("../vendors/email_msgs/notifications/" . $templateFile . ".html");
        // Special templates that are re-used
        $special_templates = array(
            'package_details' => 1,
            'package_details_client' => 1,
            'property_notes' => 1,
            'about_this_auction' => 1,
            // Special boxes that exist in the header. Place these anywhere in your template and
            // it will be placed in a td next to the Dear Firstname,
            'client_footer' => 2,
            'reservation_details' => 2,
            'purchase_details' => 2,
            'refund_details' => 2,
            'booking_request_dates' => 2,
        );

        $special_boxes = "";
        $other_boxes = "";

        $no_special = false;

        // Flag to allow certain boxes to be inline of eachother, rather than to the right of content
        if (strstr($template, "%%no_special%%") !== false && !$specialException) {
            $no_special = true;
        }

        $template = str_replace("%%no_special%%", "", $template);

        foreach ($special_templates as $k => $s) {
            if (strstr($template, "%%" . $k . "%%") !== false) {
                $pD = file_get_contents("../vendors/email_msgs/includes/" . $k . ".html");
                $pD .= "<?php \$" . $k . " = 1; ?>";

                // Special boxes that need to be in the header
                if ($s == 2) {
                    if (!$no_special) {
                        $special_boxes .= $pD;
                        $pD = "";
                        $pD .= "<?php \$special_boxes = true; ?>";
                    }
                } elseif ($s == 1) {
                    $other_boxes .= $pD;
                    $pD = "";
                }

                $template = str_replace("%%" . $k . "%%", $pD, $template);
            }
        }

        // Common footer
        $template .= file_get_contents("../vendors/email_msgs/includes/footer.html");
        $template = str_replace("%%special_boxes%%", $special_boxes, $template);
        $template = str_replace("%%other_boxes%%", $other_boxes, $template);
        $template .= file_get_contents("../vendors/email_msgs/includes/footer_" . $append . ".html");

        // LL / FG Skinning (temp hack)
        $colorsLL = array(
            '#484846', // Box header text
            '#d8d7b8', // Box header background
            '#d4d5b5', // Special box header text
            'bgcolor="#474747"', // Special box background
        );

        $colorsFG = array(
            '#b30000',
            '#f6f6f6',
            '#eeeeee',
            'bgcolor="#666666"',
        );

        if ($append == "FG") {
            $template = str_replace($colorsLL, $colorsFG, $template);
        }
        if (!$template) {
            return false;
        } else {
            return $template;
        }
    }

    private function utmLinks($template, $ppvNoticeTypeId, $append)
    {
        $this->PpvNotice->PpvNoticeType->PpvNoticeClickTrack->recursive = -1;
        $utm = $this->PpvNotice->PpvNoticeType->PpvNoticeClickTrack->findByppvNoticeTypeId($ppvNoticeTypeId);
        $utm = $utm['PpvNoticeClickTrack'];

        preg_match_all("/(href\s?=\s?[\"|'](?!#|mailto)(.*?)[\"|\'])[\s|>]/", $template, $matches);
        $matches = $matches[1];

        // Override UTM_SOURCE for PPV #28
        if ($ppvNoticeTypeId == "28" || $ppvNoticeTypeId == "54") {
            $append = "concierge";
        }

        foreach ($matches as $m) {
            // Does URL already have question mark?
            if (preg_match("/luxurylink\.com|familygetaway\.com|prefixUrl|siteUrl|dateRequestLink/", $m)) {
                $mOrig = $m;

                $whichQuote = substr($m, -1);
                $m = substr($m, 0, strlen($m) - 1);

                if (substr($m, -1) != "&") {
                    if (preg_match("/[^<]+\?[^>]+/", $m)) {
                        $m .= "&";
                    } else {
                        $m .= "?";
                    }
                }

                $m .= "showLeader=1&";

                $m .= "utm_source=" . strtolower($append) . "&";

                if ($utm['medium']) {
                    $m .= "utm_medium=" . $utm['medium'] . "&";
                }

                if ($utm['campaign']) {
                    $m .= "utm_campaign=" . $utm['campaign'];
                }

                $m .= $whichQuote;

                $template = str_replace($mOrig, $m, $template);
            }
        }

        return $template;
    }

    private function getExtranetLink($ticketId, $siteId)
    {

        if (!$ticketId || !is_numeric($ticketId)) {
            return null;
        }

        // generate the link so clients can handle res requests via extranet
        $uri = '/xnet/services/rd.php';

        if ($siteId == 1) {
            $host = Configure::read("Url.LL");
        } elseif ($siteId == 2) {
            $host = Configure::read("Url.FG");
        }

        $ts = strtotime('NOW');
        $ticketIdHash = base64_encode($ticketId);
        $tsHash = base64_encode($ts);

        $hash = md5($ticketId . $ts . 'L33T-KEY-XTRANET');

        return $host . $uri . "?z=$hash&t=$ticketIdHash&ts=$tsHash";
    }

    private function getExtranetCancellationLink($ticketId, $siteId)
    {

        if (!$ticketId || !is_numeric($ticketId)) {
            return null;
        }

        // generate the link so clients can handle res requests via extranet
        $uri = '/xnet/services/rcc.php';

        if ($siteId == 1) {
            $host = Configure::read("Url.LL");
        } elseif ($siteId == 2) {
            $host = Configure::read("Url.FG");
        }

        $ts = strtotime('NOW');
        $ticketIdHash = base64_encode($ticketId);
        $tsHash = base64_encode($ts);

        $hash = md5($ticketId . $ts . 'L33T-KEY-XTRANET');

        return $host . $uri . "?z=$hash&t=$ticketIdHash&ts=$tsHash";
    }

    public function sendPpvEmail(
        $emailTo,
        $emailFrom,
        $emailCc,
        $emailBcc,
        $emailReplyTo,
        $emailSubject,
        $emailBody,
        $ticketId,
        $ppvNoticeTypeId,
        $ppvInitials,
        $resend = false,
        $pgBookingId = null
    ) {
        if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage') || ISDEV) {
            $appendDevMessage = "\n<br />ORIGINAL TO:  $emailTo\n<br />ORIGINAL CC: $emailCc<br />ORIGINAL BCC: $emailBcc";
            $devMail = 'devmail@luxurylink.com';
            $emailTo = $emailTo ? $devMail : '';
            $emailCc = $emailCc ? $devMail : '';
            $emailBcc = $emailBcc ? $devMail : '';

            $emailBody = $emailBody . $appendDevMessage;
            $emailSubject = "DEV - " . $emailSubject;
        }

        // send out ppv and winner notification emails
        // -------------------------------------------------------------------------------

        $emailHeaders['From'] = "$emailFrom";

        // Ticket 2705 Silverpop doesn't support CC, place all CCs in the To:
        if ($emailCc) {
            $emailTo .= "," . $emailCc;
        }
        if ($emailBcc) {
            $emailTo .= "," . $emailBcc;
        }

        if (!ISDEV) {
            // Clean duplicates
            $emailTo = explode(",", $emailTo);
            $emailTo = array_unique($emailTo);
            $emailTo = implode(",", $emailTo);
        }

        if ($emailReplyTo) {
            $emailHeaders['Reply-To'] = $emailReplyTo;
        }

        $emailHeaders['Subject'] = $emailSubject;
        $emailHeaders['Content-Type'] = "text/html";
        $emailHeaders['Content-Transfer-Encoding'] = "8bit";

        // all emails through Blue Hornet
        $mailvendor = $this->MailingList->getMailVendorHelper();
        $mailvendor->sendMessage("ppv_" . $ppvNoticeTypeId, $emailTo, $emailFrom, $emailHeaders['Subject'], $emailBody);

        // below is for logging the email and updating the ticket
        // -------------------------------------------------------------------------------

        $emailSentDatetime = strtotime('now');
        $emailBodyFileName = $ticketId . '_' . $ppvNoticeTypeId . '_' . $emailSentDatetime . '.html';

        // get initials
        // -------------------------------------------------------------------------------
        if (!$ppvInitials) {
            $ppvInitials = 'N/A';
        }

        $ppvNoticeSave = array();
        $ppvNoticeSave['ppvNoticeTypeId'] = $ppvNoticeTypeId;
        $ppvNoticeSave['ticketId'] = $ticketId;
        $ppvNoticeSave['emailTo'] = $emailTo;
        $ppvNoticeSave['emailFrom'] = $emailFrom;
        $ppvNoticeSave['emailCc'] = $emailCc;
        $ppvNoticeSave['emailSubject'] = $emailSubject;
        $ppvNoticeSave['emailBody'] = $emailBody;
        $ppvNoticeSave['emailBodyFileName'] = $emailBodyFileName;
        $ppvNoticeSave['emailSentDatetime'] = date('Y-m-d H:i:s', $emailSentDatetime);
        $ppvNoticeSave['initials'] = $ppvInitials;
        $ppvNoticeSave['pgBookingId'] = $pgBookingId;

        // save the record in the database
        // -------------------------------------------------------------------------------
        $this->PpvNotice->create();
        if (!$this->PpvNotice->save($ppvNoticeSave)) {
            @mail('devmail@luxurylink.com', 'WEB SERVICE TICKETS: ppv record not saved', print_r($ppvNoticeSave, true));
        }

        if ($resend == false) {
            // update ticket status if required
            // -------------------------------------------------------------------------------
            $newTicketStatus = false;
            if ($ppvNoticeTypeId == 1 || $ppvNoticeTypeId == 23) {
                // reservation confirmation from buy now with seasonal pricing
                $currentTicketStatus = $this->Ticket->query(
                    "SELECT ticketStatusId as tsi FROM ticket WHERE ticketId = {$ticketId}"
                );
                if (isset($currentTicketStatus[0]['tsi']) && $currentTicketStatus[0]['tsi'] == 14) {
                    $newTicketStatus = 14;
                } else {
                    $newTicketStatus = 4;
                } //auction or FP

                $resData = $this->Ticket->query(
                    "SELECT * FROM reservation WHERE ticketId = $ticketId ORDER BY reservationId DESC LIMIT 1"
                );
                if (!empty($resData)) {
                    $reservationId = $resData[0]['reservation']['reservationId'];
                    $reservation = array();
                    $reservation['reservationId'] = $reservationId;
                    $reservation['ticketId'] = $ticketId;
                    $reservation['reservationConfirmToCustomer'] = date('Y:m:d H:i:s', strtotime('now'));
                    $this->Reservation->save($reservation);
                }
            } elseif (in_array($ppvNoticeTypeId, array(2, 25))) {
                // send ticket status to RESERVATION REQUESTED
                $newTicketStatus = 3;
            } elseif ($ppvNoticeTypeId == 33) {
                $newTicketStatus = 9;
            } elseif ($ppvNoticeTypeId == 10) {
                $newTicketStatus = 12;
            } elseif ($ppvNoticeTypeId == 14) {
                // DATES NOT AVAILABLE
                $newTicketStatus = 11;
            } elseif ($ppvNoticeTypeId == 29) {
                // Ticket cancellation request
                $newTicketStatus = 16;
            } elseif ($ppvNoticeTypeId == 30) {
                // Ticket cancellation confirmation
                $newTicketStatus = 17;
            } elseif ($ppvNoticeTypeId == 24) {
                // Res follow up (for FP)
                $newTicketStatus = 9;
            } elseif ($ppvNoticeTypeId == 28 || $ppvNoticeTypeId == 54) {
                // ticket #2243
                $newTicketStatus = 19;
            }

            if ($newTicketStatus) {
                $this->updateTicketStatus($ticketId, $newTicketStatus);
            }
        }

        return true;
    }

    private function updateTicketStatus($ticketId, $newStatusId)
    {
        $updateTicket = array();
        $updateTicket['ticketId'] = $ticketId;
        $updateTicket['ticketStatusId'] = $newStatusId;
        if ($this->Ticket->save($updateTicket)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function findValidUserPaymentSetting($userId, $upsId = null)
    {
        if ($upsId && is_numeric($upsId)) {
            $ups = $this->User->query(
                "SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE userId = $userId AND userPaymentSettingId = $upsId"
            );
        } else {
            $ups = $this->User->query(
                "SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE userId = $userId AND inactive = 0 ORDER BY primaryCC DESC, expYear DESC"
            );
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

    public function findUserPaymentSettingInfo($upsId)
    {
        $ups = $this->User->query(
            "SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE userPaymentSettingId = $upsId"
        );
        return (is_array($ups)) ? $ups[0] : false;
    }

    public function addTrackPending($trackId, $pendingAmount)
    {

        $track = $this->Track->read(null, $trackId);
        if (!empty($track)) {
            $track['Track']['pending'] += $pendingAmount;
            if ($this->Track->save($track['Track'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $in0
     * @return bool|string
     */
    public function processPaymentTicket($in0)
    {
        // ---------------------------------------------------------------------------
        // SUBMIT PAYMENT VIA PROCESSOR
        // ---------------------------------------------------------------------------
        // REQUIRED: (1) userId
        //           (2) ticketId
        //           (3) paymentProcessorId
        //           (4) paymentAmount
        //           (5) initials
        //           (6) autoCharge
        //           (7) saveUps
        //           (8) zAuthHashKey
        //           (9) userPaymentSettingId or userPaymentSetting data array
        //          (10) toolboxManualCharge
        //
        // SEND TO PAYMENT PROCESSOR: $userPaymentSettingPost
        // ---------------------------------------------------------------------------
        $data = json_decode($in0, true);

        // DEV NO CHARGE
        // ---------------------------------------------------------------------------
        $this->isDev = $isDev = (ISDEV || ISSTAGE);
        if (defined('CRON_ENV')) {
            if (CRON_ENV == 'dev' || CRON_ENV == 'stage') {
                $this->isDev = $isDev = true;
            }
        }

        $this->errorResponse = $this->paymentDataToProcessIsValid($data);
        if ($this->errorResponse !== false) {
            return $this->returnError(__METHOD__);
        }

        if (isset($data['toolboxManualCharge']) && ($data['toolboxManualCharge'] == 'toolbox')) {
            $toolboxManualCharge = true;
        } else {
            $toolboxManualCharge = false;
        }

        // also check the hash for more security
        // ---------------------------------------------------------------------------
        $hashCheck = $this->getAuthKeyHash(
            $data['userId'],
            $data['ticketId'],
            $data['paymentProcessorId'],
            $data['paymentAmount'],
            $data['initials']
        );

        if (trim($hashCheck) !== trim($data['zAuthHashKey'])) {
            $this->errorResponse = 109;
            return $this->returnError(__METHOD__);
        }
        unset($hashCheck);

        // and even some more error checking.
        // ---------------------------------------------------------------------------
        $this->Ticket->recursive = -1;
        $ticket = $this->Ticket->read(null, $data['ticketId']);
        $ticketId = $data['ticketId'];

        if (!$ticket) {
            $this->errorResponse = 110;
            return $this->returnError(__METHOD__);
        }
        if ($ticket['Ticket']['userId'] != $data['userId']) {
            $this->errorResponse = 111;
            return $this->returnError(__METHOD__);
        }

        // use either the data sent over or retrieve from the db with the id
        // ---------------------------------------------------------------------------
        $userPaymentSettingPost = array();

        $usingUpsId = false;
        if (isset($data['userPaymentSettingId']) && !empty($data['userPaymentSettingId']) && is_numeric(
                $data['userPaymentSettingId']
            )
        ) {
            $tmp_result = $this->Ticket->query(
                'SELECT * FROM userPaymentSetting WHERE userPaymentSettingId = ' . $data['userPaymentSettingId'] . ' LIMIT 1'
            );
            $userPaymentSettingPost['UserPaymentSetting'] = $tmp_result[0]['userPaymentSetting'];
            unset($tmp_result);
            $usingUpsId = true;
        } else {
            $userPaymentSettingPost['UserPaymentSetting'] = (isset($data['userPaymentSetting'])) ? $data['userPaymentSetting'] : 0;
        }

        if (
            !$userPaymentSettingPost
            || empty($userPaymentSettingPost)
        ) {
            $this->errorResponse = 113;
            return $this->returnError(__METHOD__);
        }

        $userPaymentSettingPost['UserPaymentSetting']['ccNumber'] =
            $this->UserPaymentSetting->detokenizeCcNum($userPaymentSettingPost['UserPaymentSetting']['ccToken']);

        // for FAMILY, payment is via PAYPAL only [override]
        // ---------------------------------------------------------------------------
        if ($ticket['Ticket']['siteId'] == 2) {
            $data['paymentProcessorId'] = 3;
        } else if ($ticket['Ticket']['useTldCurrency']) {
            // TODO: Revisit when we begin to deploy in more locales
            $data['paymentProcessorId'] = 8;
            if (!$toolboxManualCharge) {
                $data['paymentAmount'] = $ticket['Ticket']['billingPriceTld'];
            }
        }

        // set which processor to use
        // ---------------------------------------------------------------------------
        $paymentProcessorName = false;

        $paymentProcessorName = $this->PaymentProcessor->find(
            'first',
            array(
                'conditions' => array(
                    'PaymentProcessor.paymentProcessorId' => $data['paymentProcessorId']
                )
            )
        );
        $paymentProcessorName = $paymentProcessorName['PaymentProcessor']['paymentProcessorName'];

        if (!$paymentProcessorName) {
            $this->errorResponse = 114;
            return $this->returnError(__METHOD__);
        }

        // handle fees, promo discounts, etc
        // ---------------------------------------------------------------------------
        // Links GiftCert to this ticket
        $totalChargeAmount = $data['paymentAmount'];
        $payment_amt = 0;

        if ($toolboxManualCharge) {
            $fee = $this->Ticket->getFeeByTicket($ticketId);

            $totalChargeAmount = $ticket['Ticket']['billingPrice'];
            $payment_amt = $data['paymentAmount'];

            if ($data['paymentTypeId'] == 2) {
                $this->loadModel('PromoCode');
                $code = $this->PromoCode->findBypromoCode($data['ppTransactionId']);

                $this->Ticket->PromoTicketRel->create();
                $this->Ticket->PromoTicketRel->save(
                    array(
                        'ticketId' => $ticket['Ticket']['ticketId'],
                        'userId' => $ticket['Ticket']['userId'],
                        'promoCodeId' => $code['PromoCode']['promoCodeId']
                    )
                );
            }
        }

        $promoGcCofData = $this->Ticket->getPromoGcCofData(
            $ticket['Ticket']['ticketId'],
            $totalChargeAmount,
            $payment_amt,
            $toolboxManualCharge
        );

        if (!$toolboxManualCharge) {
            // this is either autocharge or user checkout

            $totalChargeAmount = $promoGcCofData['final_price'];

            // used promo or gc or cof that resulted in complete ticket price coverage -- no cc charge needed
            // -------------------------------------------------------------------------------
            if ($promoGcCofData['applied'] && ($promoGcCofData['final_price'] == 0)) {
                return $this->runPostChargeSuccess(
                    $ticket,
                    $data,
                    $usingUpsId,
                    $userPaymentSettingPost,
                    $promoGcCofData,
                    $toolboxManualCharge
                );
            }
        } else {
            $totalChargeAmount = $payment_amt;
        }

        $paymentDetail = array(
            'ticketId' => $ticket['Ticket']['ticketId'],
            'userId' => $ticket['Ticket']['userId'],
            'autoProcessed' => $data['autoCharge'],
            'initials' => $data['initials'],
            'paymentProcessorId' => $data['paymentProcessorId'],
            'paymentTypeId' => (isset($data['paymentTypeId'])) ? $data['paymentTypeId'] : null,
            'paymentAmount' => $totalChargeAmount,
            'userPaymentSettingId' => ($usingUpsId) ? $data['userPaymentSettingId'] : '',
            'ppBillingAmount' => $totalChargeAmount
        );
        if ($ticket['Ticket']['useTldCurrency'] == 1) {
            $paymentDetail['paymentAmountTld'] = $paymentDetail['paymentAmount'];
            $paymentDetail['paymentAmount'] = floor(($ticket['Ticket']['billingPrice'] / $ticket['Ticket']['billingPriceTld']) * $paymentDetail['paymentAmountTld']);
            $paymentDetail['ppBillingAmount'] = $paymentDetail['paymentAmount'];
        }

        $otherCharge = 0;

        if ((isset($data['paymentTypeId']) && $data['paymentTypeId'] == 1) || !$toolboxManualCharge) {
            // set total charge amount to send to processor
            // ---------------------------------------------------------------------------
            // init payment processing and submit payment
            // ---------------------------------------------------------------------------

            $ticket['Ticket']['billingPrice'] = $totalChargeAmount;
            $this->logError(array("PAYMENT INFO", $ticket));

            $isTestTransaction = $this->isTestTransaction(
                $userPaymentSettingPost['UserPaymentSetting']['ccNumber'],
                $ticket['Ticket']['siteId'],
                $ticket['Ticket']['offerId'],
                $isDev
            );

            $processor = new Processor($paymentProcessorName, $isTestTransaction);
            $processor->InitPayment($userPaymentSettingPost, $ticket);
            $processor->SubmitPost();

            $userPaymentSettingPost['UserPaymentSetting']['expMonth'] = str_pad(
                $userPaymentSettingPost['UserPaymentSetting']['expMonth'],
                2,
                '0',
                STR_PAD_LEFT
            );

            $paymentDetail = array_merge($paymentDetail, $processor->getModule()->getMappedResponse());
            $paymentDetail['paymentTypeId'] = 1;
            $paymentDetail['ppFirstName'] = (isset($data['firstName'])) ? $data['firstName'] : null;
            $paymentDetail['ppLastName'] = (isset($data['lastName'])) ? $data['lastName'] : null;
            $paymentDetail['ppBillingAddress1'] = $userPaymentSettingPost['UserPaymentSetting']['address1'];
            $paymentDetail['ppBillingCity'] = $userPaymentSettingPost['UserPaymentSetting']['city'];
            $paymentDetail['ppBillingState'] = $userPaymentSettingPost['UserPaymentSetting']['state'];
            $paymentDetail['ppBillingZip'] = str_replace(
                ' ',
                '',
                $userPaymentSettingPost['UserPaymentSetting']['postalCode']
            );
            $paymentDetail['ppBillingCountry'] = str_replace(
                ' ',
                '',
                $userPaymentSettingPost['UserPaymentSetting']['country']
            );
            $paymentDetail['ppCardNumLastFour'] = substr(
                $userPaymentSettingPost['UserPaymentSetting']['ccNumber'],
                -4,
                4
            );
            $paymentDetail['ppExpMonth'] = $userPaymentSettingPost['UserPaymentSetting']['expMonth'];
            $paymentDetail['ppExpYear'] = $userPaymentSettingPost['UserPaymentSetting']['expYear'];
            $paymentDetail['ccType'] = $userPaymentSettingPost['UserPaymentSetting']['ccType'];
        } else {
            if ($data['paymentTypeId'] == 2) {
                // Gift cert
                $longWord = "GIFT CERT";
                $medWord = "GIFT";
                $shortWord = "GC";
            } elseif ($data['paymentTypeId'] == 3) {
                // Credit on file
                $longWord = "CREDIT ON FILE";
                $medWord = "CRED";
                $shortWord = "CR";
            }

            // by '$longWord', I gather paymentTypeId 2 or 3 is meant
            if ($longWord) {
                $otherCharge = 1;

                $paymentDetail['paymentProcessorId'] = 6;
                $paymentDetail['ccType'] = $shortWord;
                $paymentDetail['userPaymentSettingId'] = '';
                $paymentDetail['isSuccessfulCharge'] = 1;
                $paymentDetail['autoProcessed'] = 0;
                $paymentDetail['ppFirstName'] = $data['firstName'];
                $paymentDetail['ppLastName'] = $data['lastName'];
                $paymentDetail['ppResponseDate'] = date('Y-m-d H:i:s', strtotime('now'));
                $paymentDetail['ppCardNumLastFour'] = $medWord;
                $paymentDetail['ppExpMonth'] = $shortWord;
                $paymentDetail['ppExpYear'] = $medWord;
                $paymentDetail['ppBillingAddress1'] = $longWord;
                $paymentDetail['ppBillingCity'] = $longWord;
                $paymentDetail['ppBillingState'] = $longWord;
                $paymentDetail['ppBillingZip'] = $longWord;
                $paymentDetail['ppBillingCountry'] = $longWord;
            }
        }

        // CoF & Gift being saved even if it wasn't used
        if (
            (!isset($data['paymentTypeId']) || (isset($data['paymentTypeId']) && $data['paymentTypeId'] < 2))
            && $toolboxManualCharge
        ) {
            if (isset($promoGcCofData['Cof']['applied'])) {
                $promoGcCofData['Cof']['applied'] = 0;
            }

            if (isset($promoGcCofData['GiftCert']['applied'])) {
                $promoGcCofData['GiftCert']['applied'] = 0;
            }
        }

        // save the response from the payment processor
        // ---------------------------------------------------------------------------
        $this->PaymentDetail->create();
        $tmpResult = $this->PaymentDetail->save($paymentDetail);

        if (!$tmpResult) {
            @mail(
                'devmail@luxurylink.com',
                'WEB SERVICE ERROR: PAYMENT PROCESSED BUT NOT SAVED',
                print_r($this->PaymentDetail->validationErrors, true) . print_r($paymentDetail, true)
            );
        }

        CakeLog::write(
            "web_service_tickets_controller",
            var_export(array("WEB SERVICE TICKETS: ", $paymentDetail, $promoGcCofData), 1)
        );

        // return result whether success or denied
        // ---------------------------------------------------------------------------
        if ((isset($processor) && $processor->getModule()->chargeSuccess()) || $otherCharge) {
            return $this->runPostChargeSuccess(
                $ticket,
                $data,
                $usingUpsId,
                $userPaymentSettingPost,
                $promoGcCofData,
                $toolboxManualCharge
            );
        } else {
            if ($data['paymentProcessorId'] == 1) {
                $response_txt = $processor->getModule()->getResponseTxt();
                CakeLog::write(
                    "web_service_tickets_controller",
                    "DECLINED. RESPONSE: " . var_export($processor->getModule()->getMappedResponse(), 1)
                );
                return $response_txt;
            } else {
                return false;
            }
        }
    }

    public function runPostChargeSuccess(
        $ticket,
        $data,
        $usingUpsId,
        $userPaymentSettingPost,
        $promoGcCofData,
        $toolboxManualCharge
    ) {
        $this->errorMsg = "Start";
        $this->logError(__METHOD__);


        // allocate revenue to loa and tracks
        // ---------------------------------------------------------------------------
        $tracks = $this->TrackDetail->getTrackRecord($ticket['Ticket']['ticketId']);
        if (!empty($tracks)) {
            foreach ($tracks as $track) {
                // decrement loa number of packages
                // ---------------------------------------------------------------------------
                if ($track['expirationCriteriaId'] == 2) {
                    $this->Ticket->query(
                        'UPDATE loa SET numberPackagesRemaining = numberPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1'
                    );
                } elseif ($track['expirationCriteriaId'] == 4) {
                    $this->Ticket->query(
                        'UPDATE loa SET membershipPackagesRemaining = membershipPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1'
                    );
                }

                // track detail stuff and allocation
                // ---------------------------------------------------------------------------
                $trackDetailExists = $this->TrackDetail->findExistingTrackTicket(
                    $track['trackId'],
                    $ticket['Ticket']['ticketId']
                );
                if (!$trackDetailExists) {
                    $new_track_detail = $this->TrackDetail->getNewTrackDetailRecord(
                        $track,
                        $ticket['Ticket']['ticketId']
                    );
                    if ($new_track_detail) {
                        $this->TrackDetail->create();
                        if (!$this->TrackDetail->save($new_track_detail)) {
                            mail(
                                'devmail@luxurylink.com',
                                $ticket['Ticket']['ticketId'] . ' ticket track detail not saved',
                                print_r($ticket, true)
                            );
                        }
                    } else {
                        mail(
                            'devmail@luxurylink.com',
                            $ticket['Ticket']['ticketId'] . ' ticket track detail not saved: missing site id',
                            print_r($ticket, true)
                        );
                    }
                }
            }

            $this->errorMsg = "Track Data Saved";
            $this->logError(__METHOD__);
        }

        // get data for event registry tracking
        // ---------------------------------------------------------------------------
        $eventRegistryDataResult = $this->getCreditBankAssets($ticket['Ticket']['userId']);
        $eventRegistryData['totalCreditBank'] = $eventRegistryDataResult['totalCreditBank'];
        $eventRegistryData['creditBankId'] = $eventRegistryDataResult['creditBankId'];
        $eventRegistryData['cof'] = $eventRegistryDataResult['cof'];

        $eventRegistryData['totalAmountOff'] = $promoGcCofData['Cof']['totalAmountOff'];
        $eventRegistryData['ticketId'] = $ticket['Ticket']['ticketId'];
        $eventRegistryData['userId'] = $ticket['Ticket']['userId'];
        $eventRegistryData['creditBankItemSourceId'] = 1; // 1 refers to event registry transactions
        $eventRegistryData['creditBankTransactionId'] = 2; // 2 refers to a deduction from the creditBank

        // if saving new user card information
        // ---------------------------------------------------------------------------
        if ($data['saveUps'] && !$usingUpsId && !empty($userPaymentSettingPost['UserPaymentSetting'])) {
            $userPaymentSettingPost['UserPaymentSetting']['userId'] = $ticket['Ticket']['userId'];
            $datetime = date('Y-m-d H:i:s');
            $userPaymentSettingPost['UserPaymentSetting']['ccTokenCreated'] = $datetime;
            $userPaymentSettingPost['UserPaymentSetting']['ccTokenModified'] = $datetime;

            $this->UserPaymentSetting->create();
            $this->UserPaymentSetting->save($userPaymentSettingPost['UserPaymentSetting']);
        }
        // update ticket status to FUNDED
        // ---------------------------------------------------------------------------

        $fundTicket = false;
        if ($toolboxManualCharge) {
            if ($promoGcCofData['final_price_actual'] == 0) {
                $fundTicket = true;
            }
        } else {
            $fundTicket = false;
        }

        $ticketStatusChange = array();
        if (!$toolboxManualCharge || $fundTicket) {
            $ticketStatusChange['ticketId'] = $ticket['Ticket']['ticketId'];
            $ticketStatusChange['ticketStatusId'] = 5;

            $this->errorMsg = "Ticket Status 5";
            $this->logError(__METHOD__);
        }

        // if gift cert or cof, create additional payment detail records
        // ---------------------------------------------------------------------------
        if (isset($promoGcCofData['GiftCert']) && isset($promoGcCofData['GiftCert']['applied']) && $promoGcCofData['GiftCert']['applied'] == 1) {
            $this->PaymentDetail->saveGiftCert(
                $ticket['Ticket']['ticketId'],
                $promoGcCofData['GiftCert'],
                $ticket['Ticket']['userId'],
                $data['autoCharge'],
                $data['initials'],
                $toolboxManualCharge
            );
            $this->errorMsg = "Gift Saved";
            $this->logError(__METHOD__);
        }


        if (isset($promoGcCofData['Cof']) && isset($promoGcCofData['Cof']['applied']) && $promoGcCofData['Cof']['applied'] == 1) {
            $promoGcCofData['Cof']['creditTrackingTypeId'] = 1;
            $this->PaymentDetail->saveCof(
                $ticket['Ticket']['ticketId'],
                $promoGcCofData['Cof'],
                $ticket['Ticket']['userId'],
                $data['autoCharge'],
                $data['initials'],
                $toolboxManualCharge,
                $eventRegistryData
            );
            $this->errorMsg = "CoF Saved";
            $this->logError(__METHOD__);
        }

        $this->Ticket->save($ticketStatusChange);
        $this->errorMsg = "Ticket Status Changed";
        $this->logError(__METHOD__);

        // ********* SITE NAME **********
        switch ($ticket['Ticket']['siteId']) {
            case 1:
                $siteName = 'Luxury Link';
                $url = 'http://www.luxurylink.com';
                $emailFrom = $emailReplyTo = 'referafriend@luxurylink.com';
                $headerLogo = 'http://www.luxurylink.com/images/email/LL_logo-V3.jpg';
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

        $this->errorMsg = "End CHARGE_SUCCESS";
        $this->logError(__METHOD__);
        return 'CHARGE_SUCCESS';
    }


    public function processPaymentGift($in0)
    {

        $data = json_decode($in0, true);

        $isDev = true;
        $envParam = isset($data['env']) ? $data['env'] : false;
        if ($envParam == 'prod') {
            $isDev = false;
        }

        $userPaymentSettingPost['UserPaymentSetting'] = (isset($data['userPaymentSetting'])) ? $data['userPaymentSetting'] : 0;
        $userPaymentSettingPost['UserPaymentSetting']['ccNumber'] =
            $this->UserPaymentSetting->detokenizeCcNum($userPaymentSettingPost['UserPaymentSetting']['ccToken']);

        $eventRegistryId = $data['eventRegistryId'];
        $totalChargeAmount = $data['giftAmount'];

        $ticketInfo = array();
        $ticketInfo['Ticket']['ticketId'] = $eventRegistryId . 'ER';
        $ticketInfo['Ticket']['billingPrice'] = $totalChargeAmount;

        $isTestCard = ($isDev) ? true : false;
        $processor = new Processor('NOVA', $isTestCard);

        // cvc required for gift
        $processor->getModule()->addCvc($userPaymentSettingPost['UserPaymentSetting']['cvc']);
        $processor->InitPayment($userPaymentSettingPost, $ticketInfo);

        if (!$isDev) {
            $processor->SubmitPost();
        }

        if ((isset($processor) && $processor->getModule()->chargeSuccess()) || $isDev) {
            $donationDetail = array();
            $mappedResponse = $processor->getModule()->getMappedResponse();

            $donationDetail['eventRegistryId'] = $eventRegistryId;
            $donationDetail['userId'] = $data['donorUserId'];
            $donationDetail['transactionId'] = $mappedResponse['ppTransactionId'];
            $donationDetail['personalNote'] = $data['giftMessage'];
            $donationDetail['amount'] = $totalChargeAmount;
            $donationDetail['dateCreated'] = date('Y-m-d h:i:s');
            $donationDetail['donorNameOnCard'] = $userPaymentSettingPost['UserPaymentSetting']['nameOnCard'];
            $donationDetail['donorAddress1'] = $userPaymentSettingPost['UserPaymentSetting']['address1'];
            $donationDetail['donorAddress2'] = $userPaymentSettingPost['UserPaymentSetting']['address2'];
            $donationDetail['donorCity'] = $userPaymentSettingPost['UserPaymentSetting']['city'];
            $donationDetail['donorStateName'] = $userPaymentSettingPost['UserPaymentSetting']['state'];
            $donationDetail['donorPostalCode'] = $userPaymentSettingPost['UserPaymentSetting']['postalCode'];
            $donationDetail['donorCountryCode'] = $userPaymentSettingPost['UserPaymentSetting']['country'];
            $donationDetail['ccDigits'] = substr($userPaymentSettingPost['UserPaymentSetting']['ccNumber'], -4, 4);
            $donationDetail['ccType'] = $userPaymentSettingPost['UserPaymentSetting']['ccType'];
            $donationDetail['statusId'] = 0;

            $this->EventRegistryDonor->create();
            $this->EventRegistryDonor->save($donationDetail);
            $donationId = $this->EventRegistryDonor->id;

            CakeLog::write(
                "web_service_gifts",
                var_export(array("WEB SERVICE GIFTS: ", $ticketInfo, $mappedResponse), 1)
            );

            return 'CHARGE_SUCCESS|' . $donationId;

        } else {
            $failureDetail = array();
            $mappedResponse = $processor->getModule()->getMappedResponse();

            $failureDetail['eventRegistryId'] = $eventRegistryId;
            $failureDetail['userId'] = $data['donorUserId'];
            $failureDetail['transactionId'] = $mappedResponse['ppTransactionId'];
            $failureDetail['amount'] = $totalChargeAmount;
            $failureDetail['dateCreated'] = date('Y-m-d h:i:s');
            $failureDetail['donorNameOnCard'] = $userPaymentSettingPost['UserPaymentSetting']['nameOnCard'];
            $failureDetail['donorPostalCode'] = $userPaymentSettingPost['UserPaymentSetting']['postalCode'];
            $failureDetail['ccDigits'] = substr($userPaymentSettingPost['UserPaymentSetting']['ccNumber'], -4, 4);
            $failureDetail['ccType'] = $userPaymentSettingPost['UserPaymentSetting']['ccType'];

            $this->EventRegistryGiftFailure->create();
            $this->EventRegistryGiftFailure->save($failureDetail);

            CakeLog::write("web_service_gifts", "DECLINED. RESPONSE: " . var_export($mappedResponse, 1));

            $response_txt = $processor->getModule()->getResponseTxt();
            return $response_txt;
        }
    }

    public function processGiftPostchargeSuccess($in0)
    {

        $data = json_decode($in0, true);

        $donationId = $data['donationId'];
        $transactionNumber = $data['transactionNumber'];

        $q = 'SELECT d.eventRegistryId, d.amount, d.userId AS donorUserId, d.ccDigits, d.personalNote
			  , ud.firstName AS donorFirstName, ud.lastName AS donorLastName
			  , e.eventRegistryTypeId, e.registrant1_firstName, e.registrant2_firstName, e.siteId, e.userId AS eventRegistryUserId
			  FROM eventRegistryDonor d
			  INNER JOIN eventRegistry e USING(eventRegistryId)
			  INNER JOIN user ud ON d.userId = ud.userId
			  WHERE d.statusId = 0 AND d.eventRegistryDonorId = ?';
        $result = $this->Ticket->query($q, array($donationId));

        if ($result && isset($result[0]['d'])) {

            $giftInfo = $result[0];
            $totalChargeAmount = $giftInfo['d']['amount'];
            $eventRegistryUserId = $giftInfo['e']['eventRegistryUserId'];
            $siteId = $giftInfo['e']['siteId'];

            // update COF
            $creditDetail = array();
            $creditDetail['creditTrackingTypeId'] = 6;
            $creditDetail['userId'] = $eventRegistryUserId;
            $creditDetail['amount'] = $totalChargeAmount;
            $creditDetail['notes'] = 'Registry Gift' . $transactionNumber;
            $creditDetail['ticketId'] = 0;
            $this->CreditTracking->create();
            $cofResult = $this->CreditTracking->save($creditDetail);
            $this->Ticket->query("UPDATE eventRegistryDonor SET statusId = 1 WHERE eventRegistryDonorId = $donationId");

            // update user bank
            $this->CreditBank->creditUserForEventRegistry($eventRegistryUserId, $totalChargeAmount, $donationId);
            $this->Ticket->query("UPDATE eventRegistryDonor SET statusId = 2 WHERE eventRegistryDonorId = $donationId");

            // send receipt email
            $eventRegistryName = $giftInfo['e']['registrant1_firstName'];
            if ($giftInfo['e']['registrant2_firstName'] != '') {
                $eventRegistryName .= ' & ' . $giftInfo['e']['registrant2_firstName'];
            }

            $params = array();
            $params['ppvNoticeTypeId'] = 51;
            $params['siteId'] = $siteId;
            $params['userId'] = $giftInfo['d']['donorUserId'];
            $params['send'] = 1;
            $params['returnString'] = 0;
            $params['manualEmailBody'] = 0;

            $params['transactionNumber'] = $transactionNumber;
            $params['initials'] = 'GIFT_POSTCHARGE';
            $params['eventRegistryName'] = $eventRegistryName;
            $params['giftAmount'] = $totalChargeAmount;
            $params['ccFour'] = $giftInfo['d']['ccDigits'];
            $this->ppv(json_encode($params));
            $this->Ticket->query("UPDATE eventRegistryDonor SET statusId = 3 WHERE eventRegistryDonorId = $donationId");

            // send notification email
            $params = array();
            $params['ppvNoticeTypeId'] = 50;
            $params['siteId'] = $siteId;
            $params['userId'] = $eventRegistryUserId;
            $params['send'] = 1;
            $params['returnString'] = 0;
            $params['manualEmailBody'] = 0;

            $params['giftMessage'] = $giftInfo['d']['personalNote'];
            $params['giftFromName'] = $giftInfo['ud']['donorFirstName'];
            $params['giftFromFullName'] = $giftInfo['ud']['donorFirstName'] . ' ' . $giftInfo['ud']['donorLastName'];
            $params['initials'] = 'GIFT_POSTCHARGE';
            $this->ppv(json_encode($params));
            $this->Ticket->query("UPDATE eventRegistryDonor SET statusId = 4 WHERE eventRegistryDonorId = $donationId");

            return true;
        }

        return false;
    }

    /**
     * @param $in0
     * @return string
     */
    public function getTicketAppliedPayment($in0)
    {
        $ticketData = json_decode($in0, true);
        $ticketId = $ticketData['ticketId'];

        $this->Ticket->recursive = 0;
        $ticket = $this->Ticket->read(null, $ticketId);

        $promoGcCofData = $this->Ticket->getPromoGcCofData($ticketId, $ticket['Ticket']['billingPrice']);
        $promoGcCofData['final_price'] = number_format($promoGcCofData['final_price'], 2);

        $appliedPayments = array();

        if (isset($promoGcCofData['Promo']) && isset($promoGcCofData['Promo']['applied']) && $promoGcCofData['Promo']['applied']) {
            $appliedPayments['promo']['totalAmountOff'] = $promoGcCofData['Promo']['totalAmountOff'];
            $appliedPayments['promo']['promoCode'] = $promoGcCofData['Promo']['promoCode'];
        }

        if (isset($promoGcCofData['Cof']) && isset($promoGcCofData['Cof']['applied']) && $promoGcCofData['Cof']['applied']) {
            $appliedPayments['cof']['totalAmountOff'] = $promoGcCofData['Cof']['totalAmountOff'];
        }

        if (isset($promoGcCofData['GiftCert']) && isset($promoGcCofData['GiftCert']['applied']) && $promoGcCofData['GiftCert']['applied']) {
            $appliedPayments['gc']['totalAmountOff'] = $promoGcCofData['GiftCert']['totalAmountOff'];
        }

        $appliedPayments['finalUserPrice'] = $promoGcCofData['final_price'];

        CakeLog::write("web_service_tickets_controller", var_export(array($promoGcCofData), 1));
        CakeLog::write("web_service_tickets_controller", var_export($appliedPayments, 1));

        return json_encode($appliedPayments);
    }

    public function logError($method, $msg = "")
    {
        if ($msg == "") {
            $msg = $this->errorMsg;
        }

        CakeLog::write("web_service_tickets_controller", var_export($method . ": " . $msg, 1));
    }

    public function returnError($method)
    {
        $this->logError($method, $this->errorResponse);
        return $this->errorResponse;
        exit;
    }

    public function getCreditBankAssets($userId)
    {

        // get user's credit info
        $result = $this->CreditTracking->find(
            'first',
            array(
                'conditions' => array('CreditTracking.userId' => $userId),
                'order' => array('CreditTracking.datetime' => 'DESC')
            )
        );
        $credit['cof'] = $result['CreditTracking']['balance'];

        // get user's total credit bank info
        $result = $this->CreditBank->getUserTotalAmount($userId);
        if (is_null($result)) {
            $credit['totalCreditBank'] = 0;
        } else {
            $credit['totalCreditBank'] = $result[0]['totalCreditBank'];
            $credit['creditBankId'] = $result['c']['creditBankId'];
        }

        return $credit;
    }

    /**
     * @param $data
     * @return bool|int
     */
    private function paymentDataToProcessIsValid($data)
    {
        $errorResponse = false;
        if (!isset($data['userId']) || empty($data['userId'])) {
            $errorResponse = 101;
        } else if (!isset($data['ticketId']) || empty($data['ticketId'])) {
            $errorResponse = 102;
        } else if (!isset($data['paymentProcessorId']) || !$data['paymentProcessorId']) {
            $errorResponse = 103;
        } else if (!isset($data['paymentAmount']) || $data['paymentAmount'] < 0) {
            $errorResponse = 104;
        } else if (!isset($data['initials']) || empty($data['initials'])) {
            $errorResponse = 105;
        } else if (!isset($data['autoCharge'])) {
            $errorResponse = 106;
        } else if (!isset($data['saveUps'])) {
            $errorResponse = 107;
        } else if (!isset($data['zAuthHashKey']) || !$data['zAuthHashKey']) {
            $errorResponse = 108;
        }

        return $errorResponse;
    }

    /**
     * @param $ccNumber
     * @param $siteId
     * @param $offerId
     * @param $isDev
     * @return bool
     */
    private function isTestTransaction($ccNumber, $siteId, $offerId, $isDev)
    {
        $offerTable = false;
        $clientId = false;
        $testClientId = 8455;
        $testCCNumber = '4111111111111111';

        if ($isDev === true) {
            return true;
        } else if ($ccNumber == $testCCNumber) {
            switch($siteId) {
                case 1:
                    $offerTable = 'offerLuxuryLink';
                    break;
                case 2:
                    $offerTable = 'offerFamily';
                    break;
            }

            if ($offerTable !== false) {
                $clientId = $this->Ticket->query("
                    SELECT clientId
                    FROM $offerTable
                    WHERE
                        offerId = ?
                ", array($offerId));
                $clientId = (isset($clientId[0][$offerTable]['clientId'])) ? $clientId[0][$offerTable]['clientId'] : false;
            }
        }

        return ($clientId == $testClientId);
    }
}

function wstErrorHandler($errno, $errstr, $errfile, $errline)
{
    $log = "web_service_tickets_controller";

    switch ($errno) {
        case E_ERROR:
            $eMsg = "ERROR";
            break;
        case E_PARSE:
            $eMsg = "PARSE ERROR";
            break;
        case E_CORE_ERROR:
            $eMsg = "CORE ERROR";
            break;
        case E_COMPILE_ERROR:
            $eMsg = "COMPILE ERROR";
            break;
        case E_USER_ERROR:
            $eMsg = "USER ERROR";
            break;
        case E_WARNING:
            $log = "notices";
            $eMsg = "WARNING";
            break;
        case E_NOTICE:
            $log = "notices";
            $eMsg = "NOTICE";
            break;
        case E_STRICT:
            //$eMsg = "STRICT";
            //$log = "strict";
            break;
        default:
            $eMsg = "CATCHALL";
            break;
    }

    if (isset($eMsg)) {
        $eMsg .= " [$errno] $errstr\n";
        $eMsg .= "  Line $errline in file $errfile";
        $eMsg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
        CakeLog::write($log, $eMsg);
    }

    // Don't execute PHP internal error handler
    return true;
}

function wstErrorShutdown()
{
    if (function_exists('error_get_last')) {
        $error = error_get_last();

        if ($error['type'] != 2048 && $error != null) {
            CakeLog::write("web_service_tickets_controller", "SCRIPT ABORTED: " . var_export($error, 1));
            die();
        }
    }
}



