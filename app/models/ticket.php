<?php
class Ticket extends AppModel
{
    var $name = 'Ticket';
    var $useTable = 'ticket';
    var $primaryKey = 'ticketId';
    var $actsAs = array('Containable', 'Logable');
    var $belongsTo = array(
        'TicketStatus' => array('foreignKey' => 'ticketStatusId'),
        'Package' => array('foreignKey' => 'packageId'),
        'Offer' => array('foreignKey' => 'offerId'),
        'OfferType' => array('foreignKey' => 'offerTypeId'),
        'User' => array('foreignKey' => 'userId')
    );

    public $hasMany = array(
        'PaymentDetail' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'PpvNotice' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'PromoTicketRel' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'RefundRequest' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'Call' => array(
            'foreignKey' => 'ticketId',
        ),
    );

    public $hasOne = array(
        'TicketWriteoff' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'TicketRefund' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'Reservation' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        ),
        'Cancellation' => array(
            'foreignKey' => 'ticketId',
            'dependent' => true
        )
    );

    var $validate = array(
        'totalBillingAmount' => array('rule' => array('money', 'left')),
        'userId' => array(
            'validateRegisteredUser' => array(
                'rule' => array('validateRegisteredUser'),
                'message' => 'The UserId is Invalid. Please ensure that the UserId belongs to a registered User.',
                'allowEmpty' => true,
            ),
        ),
    );

    function beforeFind($options)
    {
        if (!is_array($options['fields']) && strpos($options['fields'], 'COUNT(*)') !== false) {
            $options['recursive'] = -1;
        }

        return $options;
    }

    // override paginate count only for tickets!
    function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        $params = array('conditions' => $conditions);
        foreach ($conditions as $k => $v) {
            if (stristr($k, 'promo')) {
                $params['contain'][] = 'PromoTicketRel';
                $params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
            }
            if (stristr($k, 'reservation')) {
                $params['contain'][] = 'Reservation';
                $params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
            }
        }
        if (isset($extra['joins'])) { // just for client search in ticket
            $params['joins'] = $extra['joins'];
            $params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
            $params['contain'] = $extra['contain'];
        }
        if (isset($extra['group'])) {
            if (stristr($extra['group'][0], 'rescount')) {
                $params['fields'][] = 'COUNT(PpvNotice.ppvNoticeId) AS rescount';
                $params['group'] = $extra['group'];
                $result = $this->find('all', $params);
                return count($result);
            }
        }
        $result = $this->find('count', $params);
        return $result;
    }

    function getClientContacts($ticketId, $clientId = null)
    {
        $contacts = array();
        $sql = "SELECT c.clientId, c.parentClientId FROM ticket t ";
        $sql .= "INNER JOIN clientLoaPackageRel clpr USING (packageId) ";
        $sql .= "INNER JOIN client c ON clpr.clientId = c.clientId ";
        $sql .= "WHERE t.ticketId = $ticketId ";
        if ($clientId) {
            $sql .= "AND clpr.clientId = {$clientId}";
        }
        $result = $this->query($sql);
        if (!empty($result)) {
            $client = $result[0]['c'];
            if (!empty($client['parentClientId']) && is_numeric(
                    $client['parentClientId']
                ) && ($client['parentClientId'] > 0) && ($client['clientId'] != $client['parentClientId'])
            ) {
                $add_parent_client_sql = "OR clientId = " . $client['parentClientId'];
            } else {
                $add_parent_client_sql = '';
            }
            $contact_to_string = $contact_cc_string = array();
            $tmp_result = $this->query(
                "SELECT * FROM clientContact WHERE clientContactTypeId in (1,3) AND (clientId = " . $client['clientId'] . " $add_parent_client_sql) ORDER BY clientContactTypeId, primaryContact DESC"
            );
            foreach ($tmp_result as $a => $b) {
                if ($b['clientContact']['clientContactTypeId'] == 1) {
                    $contact_to_string[] = $b['clientContact']['emailAddress'];
                }
                if ($b['clientContact']['clientContactTypeId'] == 3) {
                    $contact_cc_string[] = $b['clientContact']['emailAddress'];
                }
            }
            $contacts['contact_cc_string'] = implode(',', array_unique($contact_cc_string));
            $contacts['contact_to_string'] = implode(',', array_unique($contact_to_string));
            if (!$contacts['contact_to_string'] && !empty($contacts['contact_cc_string'])) {
                $contacts['contact_to_string'] = $contacts['contact_cc_string'];
                $contacts['contact_cc_string'] = '';
            }
        }
        return $contacts;
    }

    function findPromoOfferTrackings($userId, $offerId)
    {
        $result = $this->query(
            "SELECT promoCodeId FROM promoOfferTracking where userId = $userId and offerId = $offerId"
        );
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
    }

    function findPromoOfferTrackingsExtended($userId, $offerId)
    {
        $result = $this->query(
            "SELECT promoCodeId, creditBlockedFlag FROM promoOfferTracking where userId = $userId and offerId = $offerId"
        );
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
    }

    function getTicketPromoData($ticketId)
    {
        $sql = "SELECT pc.*, p.promoName, p.amountOff, p.percentOff FROM promoTicketRel ptr ";
        $sql .= "INNER JOIN promoCode pc ON pc.promoCodeId = ptr.promoCodeId ";
        $sql .= "INNER JOIN promoCodeRel pcr ON pcr.promoCodeId = pc.promoCodeId ";
        $sql .= "INNER JOIN promo p on p.promoId = pcr.promoId ";
        $sql .= "WHERE ptr.ticketId = $ticketId";
        $result = $this->query($sql);
        return $result;
    }

    function getPromoGcCofData($ticketId, $ticketPrice, $payment_amt = 0, $manualCharge = false)
    {
        $data = array();
        $data['original_ticket_price'] = $ticketPrice;
        $data['payment_amt'] = $payment_amt;
        $data['payments'] = 0;

        // get the promo code that is applied to this ticket
        $q = "SELECT PromoCode.*, Promo.*, PromoTicketRel.creditBlockedFlag
								FROM promoTicketRel as PromoTicketRel 
								LEFT JOIN promoCode AS PromoCode ON PromoTicketRel.promoCodeId = PromoCode.promoCodeId 
								LEFT JOIN promoCodeRel AS PromoCodeRel ON PromoCode.promocodeId = PromoCodeRel.promoCodeId 
								LEFT JOIN promo AS Promo ON PromoCodeRel.promoId = Promo.promoId 
								WHERE PromoTicketRel.ticketId = $ticketId 
								GROUP BY PromoTicketRel.promoCodeId";
        $result = $this->query($q);

        // loop over the result set
        // and overwrite 'Promo' if it is a promo with an array of the last result set
        // if it is a 'GiftCert', overwrite 'GiftCert' with the last result set that contains the balance
        foreach ($result as $k => $row) {
            if ($row['PromoCode']['promoCodeId'] && $row['Promo']['promoId']) {
                $data['Promo'] = array_merge($row['PromoCode'], $row['Promo'], $row['PromoTicketRel']);
            } else {
                $gcSql = 'SELECT * FROM giftCertBalance ';
                $gcSql .= 'WHERE promoCodeId = ' . $row['PromoCode']['promoCodeId'] . ' ';
                $gcSql .= 'ORDER BY giftCertBalanceId DESC LIMIT 1';

                $gcResult = $this->query($gcSql);
                if (!empty($gcResult) && ($gcResult[0]['giftCertBalance']['balance'] > 0)) {
                    $data['GiftCert'] = $gcResult[0]['giftCertBalance'];
                }
            }
        }

        $blockCreditOnFile = false;

        // if there is a promo and a ticket price...
        if (isset($data['Promo']) && $data['Promo'] && ($ticketPrice > 0)) {
            // get the amount off for the ticket
            if ($data['Promo']['percentOff'] && !$data['Promo']['amountOff']) {
                // for percent off
                $data['Promo']['totalAmountOff'] = ($ticketPrice * ($data['Promo']['percentOff'] / 100));
            } elseif (!$data['Promo']['percentOff'] && $data['Promo']['amountOff']) {
                // for amount off
                $data['Promo']['totalAmountOff'] = intval($data['Promo']['amountOff']);
            }
            $data['Promo']['applied'] = ($data['Promo']['totalAmountOff'] > 0) ? 1 : 0;

            // ticket 3384
            if ($data['Promo']['creditBlockedFlag'] == 1) {
                $blockCreditOnFile = true;
            }
        }

        if ($this->isForeignCurrencyTicket($ticketId) === true) {
            $blockCreditOnFile = true;
        }

        // get the handling fee
        if ($ticketPrice > 0) {
            $fee = $this->getFeeByTicket($ticketId);
            $ticketPrice += $fee;
        }

        // if it is a GiftCert and ticketPrice is greater than 0
        // make sure the deduction doesn't bring it lower than 0
        // set the remaining balance after use of the gift cert
        if (isset($data['GiftCert']) && $data['GiftCert'] && $ticketPrice > 0) {
            $new_price = $ticketPrice - $data['GiftCert']['balance'];
            if ($new_price <= 0) {
                $data['GiftCert']['totalAmountOff'] = $ticketPrice;
                $data['GiftCert']['remainingBalance'] = abs($new_price);
                $new_price = 0;
            } else {
                $data['GiftCert']['totalAmountOff'] = $data['GiftCert']['balance'];
                $data['GiftCert']['remainingBalance'] = false;
            }

            $data['GiftCert']['applied'] = ($data['GiftCert']['totalAmountOff'] > 0) ? 1 : 0;
        } else {
            $data['GiftCert']['totalAmountOff'] = 0;
        }

        // if clause added for ticket 3384
        if (!$blockCreditOnFile) {

            // This is commented out because CreditTracking does not determine if the credit
            // was used against the ticket, paymentDetail with paymentTypeId of 3 determines
            // if the credit was applied to the ticket
            //
            // see if the user has any credit on file
            // the last row in the table will be the sum total of the credit they have
            // may be multiple rows per user with debits and credits

            $cofSql = 'SELECT CreditTracking.creditTrackingId,CreditTracking.balance,CreditTracking.amount FROM ticket AS Ticket ';
            $cofSql .= 'INNER JOIN creditTracking AS CreditTracking USING (userId) ';
            $cofSql .= "WHERE Ticket.ticketId = $ticketId ";
            $cofSqlEnd = " ORDER BY CreditTracking.creditTrackingId DESC";

            $cofResult = $this->query($cofSql . $cofSqlEnd . " LIMIT 1");

            if (!empty($cofResult) && ($cofResult[0]['CreditTracking']['balance'] > 0)) {
                $data['Cof'] = $cofResult[0]['CreditTracking'];
            }
        }

        // if there is a credit on file and a ticketPrice
        // deduct the credit from the ticketPrice
        if (isset($data['Cof']) && $ticketPrice > 0) {
            if (!$manualCharge) {
                // Retrieve credits related to this ticket

                $this->CreditTracking = ClassRegistry::init("CreditTracking");
                $related = $this->query(
                    "SELECT
                                    CreditTracking.creditTrackingId,CreditTracking.balance,CreditTracking.amount
                                    FROM creditTracking CreditTracking
                                    WHERE CreditTracking.creditTrackingId IN (SELECT creditTrackingId FROM creditTrackingTicketRel
                                        WHERE ticketId = " . intval($ticketId) . ") ORDER BY CreditTracking.creditTrackingId DESC LIMIT 1"
                );

                // If this ticket has an existing CoF entry, use that as the definitive source for CoF balance
                if (!empty($related)) {
                    $data['Cof'] = $related[0]['CreditTracking'];
                }
            }

            $new_price = $ticketPrice - $data['Cof']['balance'];
            if ($new_price <= 0) {
                $data['Cof']['totalAmountOff'] = $ticketPrice;
                $data['Cof']['remainingBalance'] = abs($new_price);
                $new_price = 0;
            } else {
                $data['Cof']['totalAmountOff'] = $data['Cof']['balance'];
                $data['Cof']['remainingBalance'] = false;
            }

            $data['Cof']['applied'] = ($data['Cof']['totalAmountOff'] > 0) ? 1 : 0;
        } else {
            $data['Cof']['totalAmountOff'] = 0;
            $data['Cof']['remainingBalance'] = false;
        }

        // see if they used a gift certificate on this ticket
        $paymentAmountColumn = (!$this->isForeignCurrencyTicket($ticketId)) ? 'paymentAmount' : 'paymentAmountTld';

        $paymentRecordSql = "SELECT paymentTypeId, $paymentAmountColumn AS paymentAmount FROM paymentDetail ";
        $paymentRecordSql .= "WHERE ticketId = $ticketId AND isSuccessfulCharge = 1";
        $paymentRecordResult = $this->query($paymentRecordSql);

        if (!empty($paymentRecordResult)) {
            // loop over the result set from payementDetail with the gift cert data
            // set GiftCert applied to 1
            // set totalAmountOff to paymentAmount retrieved from the db
            // deduct paymentAmount from the ticketPrice
            $noMoreGift = 0;
            foreach ($paymentRecordResult as $payment) {
                if ($payment['paymentDetail']['paymentTypeId'] == 2 && $noMoreGift == 0) {
                    $data['GiftCert']['applied'] = 1;
                    $data['GiftCert']['totalAmountOff'] = $payment['paymentDetail']['paymentAmount'];

                    // Only one gift cert per ticket
                    $noMoreGift = 1;
                } elseif ($payment['paymentDetail']['paymentTypeId'] == 3) {
                    $data['Cof']['applied'] = 1;
                    $data['Cof']['totalAmountOff'] += $payment['paymentDetail']['paymentAmount'];
                    $data['Cof']['remainingBalance'] -= $payment['paymentDetail']['paymentAmount'];
                }

                $data['payments'] += $payment['paymentDetail']['paymentAmount'];
            }
        }

        $data['Cof']['totalAmountOff'] = abs($data['Cof']['totalAmountOff']);
        $data['GiftCert']['totalAmountOff'] = abs($data['GiftCert']['totalAmountOff']);

        if (!isset($data['Cof']['remainingBalance'])) {
            $data['Cof']['remainingBalance'] = 0;
        } else {
            $data['Cof']['remainingBalance'] = ($data['Cof']['remainingBalance'] < 0) ? 0 : $data['Cof']['remainingBalance'];
        }

        if (!isset($data['GiftCert']['remainingBalance'])) {
            $data['GiftCert']['remainingBalance'] = 0;
        } else {
            $data['GiftCert']['remainingBalance'] = ($data['GiftCert']['remainingBalance'] < 0) ? 0 : $data['GiftCert']['remainingBalance'];
        }

        $data['final_price'] = ($ticketPrice < 0 ? 0 : $ticketPrice);

        // This is the final price WITHOUT CoF applied and Gift applied, and payments deducted
        // rvella
        $original_ticket_price_Calc = round($data['original_ticket_price'], 2);
        $promoAmount_Calc = isset($data['Promo']['totalAmountOff']) ? round($data['Promo']['totalAmountOff'], 2) : 0;
        $payment_amt_Calc = round($payment_amt, 2);
        $payments_Calc = round($data['payments'], 2);
        $data['final_price_actual'] = $original_ticket_price_Calc - $promoAmount_Calc - $payment_amt_Calc - $payments_Calc + $fee;
        $data['final_price_actual'] = $data['final_price_actual'] < 0 ? 0 : $data['final_price_actual'];

        $ticketPrice -= (isset($data['Promo']['totalAmountOff']) ? $data['Promo']['totalAmountOff'] : 0);

        if ($data['GiftCert']['totalAmountOff'] > $ticketPrice) {
            $data['GiftCert']['totalAmountOff'] = $ticketPrice;
        }
        $ticketPrice -= $data['GiftCert']['totalAmountOff'];

        if ($data['Cof']['totalAmountOff'] > $ticketPrice) {
            $data['Cof']['totalAmountOff'] = $ticketPrice;
        }
        $ticketPrice -= $data['Cof']['totalAmountOff'];

        $data['applied'] = (!empty($data['Promo']['applied']) || !empty($data['GiftCert']['applied']) || isset($data['Cof']['applied'])) ? 1 : 0;
        $data['final_price'] = ($ticketPrice < 0 ? 0 : $ticketPrice);

        return $data;

    }

    public function getFeeByTicket($ticketId)
    {
        $tldId = $this->getTldIdByTicketId($ticketId);
        if ($tldId == 1) {
            $fee = 40;
        } else if ($tldId == 2) {
            $fee = 25;
        }

        return $fee;
    }

    public function getCurrencyNameByTicketId($ticketId)
    {
        $tldId = $this->getTldIdByTicketId($ticketId);

        if ($tldId == 1) {
            $currencyName = "USD";
        } else if ($tldId == 2) {
            $currencyName =  "GBP";
        }

        return $currencyName;
    }

    public function getCurrencySymbolByTicketId($ticketId)
    {
        $tldId = $this->getTldIdByTicketId($ticketId);

        if ($tldId == 1) {
            $currencySymbol = "$";
        } else if ($tldId == 2) {
            $currencySymbol =  "&pound;";
        }

        return $currencySymbol;
    }

    public function getTldIdByTicketId($ticketId)
    {
        $sql = "SELECT tldId, useTldCurrency FROM ticket WHERE ticketId=?";
        $params = array($ticketId);

        $result = $this->query($sql, $params);
        $useTldCurrency = isset($result[0]['ticket']['useTldCurrency']) ? $result[0]['ticket']['useTldCurrency'] : 0;
        $tldId = isset($result[0]['ticket']['tldId']) ? $result[0]['ticket']['tldId'] : 1;

        // Override to USD if Ticket.useTldCurrency is not 1
        if ($useTldCurrency != 1) {
            $tldId = 1;
        }

        return $tldId;
    }

    function isMultiProductPackage($ticketId)
    {
        $sql = "SELECT COUNT(DISTINCT clientId) AS COUNT FROM clientLoaPackageRel INNER JOIN ticket using (packageId) WHERE ticketId = $ticketId";
        $result = $this->query($sql);
        if ($result[0][0]['COUNT'] > 1) {
            return true;
        } else {
            return false;
        }
    }

    function getClientsFromPackageId($packageId)
    {
        $sql = 'SELECT Client.clientId, Client.name, Client.clientTypeId, Client.parentClientId FROM clientLoaPackageRel cr INNER JOIN client as Client ON Client.clientId = cr.clientId WHERE cr.packageId = ' . $packageId;
        $clients = $this->query($sql);
        return $clients;
    }

    function getDerivedPackageNumSales($packageId)
    {
        $sql = "SELECT count(*) AS COUNT FROM ticket INNER JOIN paymentDetail pd ON ticket.ticketId = pd.ticketId AND pd.isSuccessfulCharge = 1 ";
        $sql .= "WHERE ticket.packageId = $packageId AND ticket.ticketStatusId NOT IN (7,8)";
        $result = $this->query($sql);
        if (!empty($result) && isset($result[0][0]['COUNT']) && is_numeric($result[0][0]['COUNT'])) {
            return $result[0][0]['COUNT'];
        } else {
            return false;
        }
    }

    function getNumBuyNowRequests($packageId)
    {
        // ticketStatusId 3	= Reservation Requested
        $q = "SELECT COUNT(*) as numRequests ";
        $q .= "FROM ticket WHERE packageId=$packageId AND offerTypeId=1 AND ticketStatusId=3";
        $rows = $this->query($q);
        if (count($rows) > 0) {
            return $rows[0][0]['numRequests'];
        } else {
            return 0;
        }

    }

    function __isValidPackagePromo($packagePromoId, $packageId)
    {
        $result = $this->query(
            "SELECT count(*) as C FROM packagePromoRel WHERE packagePromoId = $packagePromoId AND packageId = $packageId"
        );
        if ($result[0][0]['C'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getPackageNumMaxSales($packageId)
    {
        $sql = "SELECT maxNumSales FROM package WHERE packageId = $packageId";
        $result = $this->query($sql);
        if (!empty($result) && isset($result[0]['package']['maxNumSales']) && is_numeric(
                $result[0]['package']['maxNumSales']
            ) && ($result[0]['package']['maxNumSales'] > 0)
        ) {
            return ($result[0]['package']['maxNumSales']);
        } else {
            return false;
        }
    }

    function sendDebugEmail($title, $data)
    {
        @mail('devmail@luxurylink.com', "SCHEDULING FLAGS DEBUG: $title", print_r($data, true));
    }

    // get expiration criteria with only the ticketId
    function getExpirationCriteria($ticketId)
    {
        $check_exp_crit = "SELECT track.expirationCriteriaId FROM ticket INNER JOIN offer USING(offerId)
						   INNER JOIN schedulingInstance USING(schedulingInstanceId) 
						   INNER JOIN schedulingMaster USING(schedulingMasterId) 
						   INNER JOIN schedulingMasterTrackRel USING(schedulingMasterId) 
						   INNER JOIN track USING (trackId) 
						   WHERE ticket.ticketId = $ticketId";
        //CakeLog::write("debug","expCritId query $check_exp_crit");
        $result = $this->query($check_exp_crit);
        return $result[0]['track']['expirationCriteriaId'];
    }

    function __runTakeDownRetailValue($clientId, $offerRetailValue, $ticketId)
    {
        // THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 5!!!
        // TODO:  this is temporary before SOA is in place

        if (!$offerRetailValue || !$clientId) {
            return false;
        }

        $sql = "SELECT loa.retailValueBalance, package.packageId, package.approvedRetailPrice, GROUP_CONCAT(schedulingMasterId) AS smids FROM loa
				INNER JOIN track USING (loaId) 
				INNER JOIN schedulingMasterTrackRel USING (trackId) 
				INNER JOIN schedulingMaster USING (schedulingMasterid) 
				INNER JOIN package using (packageId) 
				WHERE track.expirationCriteriaId = 5 AND loa.clientId = $clientId GROUP BY packageId;";
        $result = $this->query($sql);

        $pids = array();
        foreach ($result as $k => $v) {
            if ($v['loa']['retailValueBalance'] - $offerRetailValue - $v['package']['approvedRetailPrice'] <= 0) {
                // these packages will cause loa.retailValueBalance to be less or equal to 0
                // kill them packages!
                if ($this->__runTakeDown($v[0]['smids'])) {
                    $pids[] = $v['package']['packageId'];
                }
            }
        }
        if (!empty($pids)) {
            $this->insertMessageQueuePackage($ticketId, 'RETAIL_VALUE', $pids);
        }
    }

    function __runTakeDownLoaMemBal($packageId, $ticketId, $ticketAmount)
    {
        // check to make sure LOA balance is fulfilled
        // THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 1!!!

        $loas = $this->query(
            "SELECT clpr.*, loa.*, track.* FROM clientLoaPackageRel clpr
							INNER JOIN loa ON clpr.loaId = loa.loaId 
							INNER JOIN schedulingMaster sm ON clpr.packageId = sm.packageId 
							INNER JOIN schedulingMasterTrackRel smtr ON sm.schedulingMasterId = smtr.schedulingMasterId 
							INNER JOIN track ON smtr.trackId = track.trackId AND track.expirationCriteriaId = 1 
							WHERE clpr.packageId = $packageId 
							GROUP BY clpr.loaId"
        );

        foreach ($loas as $loa) {

            // if not expiration criteria id 1 "membership fee"
            // ------------------------------------------------------------------
            if ($loa['track']['expirationCriteriaId'] != 1) {
                continue;
            }

            // set some LOA data
            // ------------------------------------------------------------------
            $loa_id = $loa['loa']['loaId'];
            $loa_m_balance = $loa['loa']['membershipBalance'];
            $ticket_amount_adjusted = ($ticketAmount * $loa['clpr']['percentOfRevenue']) / 100;

            if (($loa_m_balance - $ticket_amount_adjusted) <= 0) {
                $sql = 'SELECT smtr.schedulingMasterId FROM track t ';
                $sql .= 'INNER JOIN schedulingMasterTrackRel smtr USING (trackId) ';
                $sql .= "WHERE t.loaId = $loa_id AND t.expirationCriteriaId = 1";
                $result = $this->query($sql);
                if (!empty($result)) {
                    $sm_ids = array();
                    foreach ($result as $row) {
                        $sm_ids[] = $row['smtr']['schedulingMasterId'];
                    }
                    $sm_ids_imp = implode(',', $sm_ids);
                    if ($this->__runTakeDown($sm_ids_imp)) {
                        $debug = array();
                        $debug['INPUT']['packageId'] = $packageId;
                        $debug['INPUT']['ticketId'] = $ticketId;
                        $debug['INPUT']['ticketAmount'] = $ticketAmount;
                        $debug['DATA']['Loa'] = $loa;
                        $debug['DATA']['Loas'] = $loas;
                        $debug['DATA']['ticket_amount_adjusted'] = $ticket_amount_adjusted;
                        $debug['DATA']['loa_m_balance'] = $loa_m_balance;
                        $debug['DATA']['sm_ids'] = $sm_ids;
                        $this->sendDebugEmail('LOA_BALANCE', $debug);
                        $this->insertMessageQueuePackage($ticketId, 'LOA_BALANCE');
                    }
                }
            }
        }
    }

    function __runTakeDownLoaNumPackages($packageId, $ticketId)
    {
        // check to make sure LOA membership num packages is fulfilled
        // THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 4!!!

        $loas = $this->query(
            "SELECT clpr.*, loa.*, track.* FROM clientLoaPackageRel clpr
							INNER JOIN loa ON clpr.loaId = loa.loaId 
							INNER JOIN schedulingMaster sm ON clpr.packageId = sm.packageId 
							INNER JOIN schedulingMasterTrackRel smtr ON sm.schedulingMasterId = smtr.schedulingMasterId 
							INNER JOIN track ON smtr.trackId = track.trackId AND track.expirationCriteriaId = 4 
							WHERE clpr.packageId = $packageId 
							GROUP BY clpr.loaId"
        );

        foreach ($loas as $loa) {

            // if not expiration criteria id 4 "membership # of packages"
            // ------------------------------------------------------------------
            if ($loa['track']['expirationCriteriaId'] != 4) {
                continue;
            }

            // set some LOA data
            // ------------------------------------------------------------------
            $loa_id = $loa['loa']['loaId'];
            $loa_m_total_packages = $loa['loa']['membershipTotalPackages'];
            $take_down = false;
            $take_down_fp = false;

            // get all packageId's on membership balance tracks for this LOA
            // ------------------------------------------------------------------
            $sql = "SELECT packageId FROM clientLoaPackageRel clpr ";
            $sql .= "INNER JOIN schedulingMaster sm USING(packageId) ";
            $sql .= "INNER JOIN schedulingMasterTrackRel smtr USING (schedulingMasterId) ";
            $sql .= "INNER JOIN track t ON smtr.trackId = t.trackId AND t.expirationCriteriaId = 4 ";
            $sql .= "WHERE clpr.loaId = $loa_id GROUP BY clpr.packageId";
            $result = $this->query($sql);
            if (!empty($result)) {
                $package_ids = array();
                foreach ($result as $clpr) {
                    $package_ids[] = $clpr['clpr']['packageId'];
                }
                $package_ids_imp = implode(',', $package_ids);
            } else {
                continue;
            }

            // check LOA packages
            // ------------------------------------------------------------------
            if (is_numeric($loa_m_total_packages) && ($loa_m_total_packages > 0)) {
                $sql = "SELECT count(DISTINCT ticket.ticketId) AS COUNT FROM ticket INNER JOIN paymentDetail pd ON ticket.ticketId = pd.ticketId AND pd.isSuccessfulCharge = 1 ";
                $sql .= "WHERE ticket.ticketStatusId NOT IN (7,8) AND ticket.packageId IN ($package_ids_imp)";
                $result = $this->query($sql);
                if (!empty($result)) {
                    $loa_packages_derived = $result[0][0]['COUNT'];
                    if (($loa_packages_derived + 1) >= $loa_m_total_packages) {
                        $take_down = true;
                    } elseif (($loa_m_total_packages - ($loa_packages_derived + 1) == 1)) {
                        $take_down_fp = true;
                    }
                }
            }

            // take down those scheduling masters and instances
            // ------------------------------------------------------------------
            if ($take_down || $take_down_fp) {
                $result = $this->query(
                    "SELECT schedulingMasterId FROM schedulingMaster WHERE packageId IN ($package_ids_imp)"
                );
                if (!empty($result)) {
                    $sm_ids = array();
                    foreach ($result as $row) {
                        $sm_ids[] = $row['schedulingMaster']['schedulingMasterId'];
                    }
                    $sm_ids_imp = implode(',', $sm_ids);
                    if ($take_down) {
                        if ($this->__runTakeDown($sm_ids_imp)) {
                            $debug = array();
                            $debug['INPUT']['packageId'] = $packageId;
                            $debug['INPUT']['ticketId'] = $ticketId;
                            $debug['DATA']['loa'] = $loa;
                            $debug['DATA']['loas'] = $loas;
                            $debug['DATA']['loa_m_total_packages'] = $loa_m_total_packages;
                            $debug['DATA']['package_ids'] = $package_ids;
                            $debug['DATA']['loa_packages_derived'] = $loa_packages_derived;
                            $debug['DATA']['sm_ids'] = $sm_ids;
                            $this->sendDebugEmail('LOA_PACKAGES', $debug);
                            $this->insertMessageQueuePackage($ticketId, 'LOA_PACKAGES');
                        }
                    } elseif ($take_down_fp) {
                        if ($this->__updateSchedulingOfferFixedPrice($sm_ids_imp)) {
                            $debug = array();
                            $debug['INPUT']['packageId'] = $packageId;
                            $debug['INPUT']['ticketId'] = $ticketId;
                            $debug['DATA']['loa'] = $loa;
                            $debug['DATA']['loas'] = $loas;
                            $debug['DATA']['loa_m_total_packages'] = $loa_m_total_packages;
                            $debug['DATA']['package_ids'] = $package_ids;
                            $debug['DATA']['loa_packages_derived'] = $loa_packages_derived;
                            $debug['DATA']['sm_ids'] = $sm_ids;
                            $this->sendDebugEmail('LOA_PACKAGES_FP_ONLY', $debug);
                            $this->insertMessageQueuePackage($ticketId, 'LOA_PACKAGES_FP_ONLY');
                        }
                    }
                }
            }
        }
    }

    function getSmIdsFromPackage($packageId)
    {
        $smids = array();
        $data = $this->query("SELECT schedulingMasterId FROM schedulingMaster WHERE packageId = $packageId");
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $smids[] = $v['schedulingMaster']['schedulingMasterId'];
            }
        }
        return $smids;
    }

    function __runTakeDownPricePointNumPackages($pricePointId, $ticketId)
    {
        $sql = "SELECT maxNumSales FROM pricePoint WHERE pricePointId = $pricePointId";
        $result = $this->query($sql);
        if (!empty($result) && isset($result[0]['pricePoint']['maxNumSales']) && is_numeric(
                $result[0]['pricePoint']['maxNumSales']
            ) && ($result[0]['pricePoint']['maxNumSales'] > 0)
        ) {
            $pricePointMaxNumSales = ($result[0]['pricePoint']['maxNumSales']);
        } else {
            $pricePointMaxNumSales = false;
        }

        $sql = "SELECT count(*) AS COUNT FROM pricePoint
				INNER JOIN ticket USING(packageId) 
				INNER JOIN paymentDetail pd ON pd.ticketId = ticket.ticketId AND pd.isSuccessfulCharge = 1                  
				WHERE pricePointId = $pricePointId AND ticket.ticketStatusId NOT IN (7,8);";
        $result = $this->query($sql);
        if (!empty($result) && isset($result[0][0]['COUNT']) && is_numeric($result[0][0]['COUNT'])) {
            $derived = $result[0][0]['COUNT'];
        } else {
            $derived = false;
        }

        if (($pricePointMaxNumSales !== false) && ($derived !== false)) {
            $smids = array();
            $data = $this->query("SELECT schedulingMasterId FROM schedulingMaster WHERE pricePointId = $pricePointId");
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $smids[] = $v['schedulingMaster']['schedulingMasterId'];
                }
            }
            if (!empty($smids)) {
                $schedulingMasterIds = implode(',', $smids);
                if (($derived + 1) >= $pricePointMaxNumSales) {
                    if ($this->__runTakeDown($schedulingMasterIds)) {
                        $debug = array();
                        $debug['INPUT']['pricePointId'] = $pricePointId;
                        $debug['INPUT']['ticketId'] = $ticketId;
                        $debug['DATA']['maxNumSales'] = $pricePointMaxNumSales;
                        $debug['DATA']['derived'] = $derived;
                        $debug['DATA']['smids'] = $smids;
                        $this->insertMessageQueuePackage($ticketId, 'PRICEPOINT');
                    }
                } elseif (($pricePointMaxNumSales - ($derived + 1) == 1)) {
                    if ($this->__updateSchedulingOfferFixedPrice($schedulingMasterIds)) {
                        $debug = array();
                        $debug['INPUT']['pricePointId'] = $pricePointId;
                        $debug['INPUT']['ticketId'] = $ticketId;
                        $debug['DATA']['maxNumSales'] = $pricePointMaxNumSales;
                        $debug['DATA']['derived'] = $derived;
                        $debug['DATA']['smids'] = $smids;
                        $this->insertMessageQueuePackage($ticketId, 'PRICEPOINT_FP_ONLY');
                    }
                }
            }
        }
    }

    function __runTakeDownPackageNumPackages($packageId, $ticketId)
    {
        $packageMaxNumSales = $this->getPackageNumMaxSales($packageId);
        $derivedPackageNumSales = $this->getDerivedPackageNumSales($packageId);

        if (($packageMaxNumSales !== false) && ($derivedPackageNumSales !== false)) {
            $smids = $this->getSmIdsFromPackage($packageId);
            if (!empty($smids)) {
                $schedulingMasterIds = implode(',', $smids);
                if (($derivedPackageNumSales + 1) >= $packageMaxNumSales) {
                    if ($this->__runTakeDown($schedulingMasterIds)) {
                        $debug = array();
                        $debug['INPUT']['packageId'] = $packageId;
                        $debug['INPUT']['ticketId'] = $ticketId;
                        $debug['DATA']['packageMaxNumSales'] = $packageMaxNumSales;
                        $debug['DATA']['derivedPackageNumSales'] = $derivedPackageNumSales;
                        $debug['DATA']['smids'] = $smids;
                        $this->sendDebugEmail('PACKAGE', $debug);
                        $this->insertMessageQueuePackage($ticketId, 'PACKAGE');
                        return true;
                    }
                } elseif (($packageMaxNumSales - ($derivedPackageNumSales + 1) == 1)) {
                    if ($this->__updateSchedulingOfferFixedPrice($schedulingMasterIds)) {
                        $debug = array();
                        $debug['INPUT']['packageId'] = $packageId;
                        $debug['INPUT']['ticketId'] = $ticketId;
                        $debug['DATA']['packageMaxNumSales'] = $packageMaxNumSales;
                        $debug['DATA']['derivedPackageNumSales'] = $derivedPackageNumSales;
                        $debug['DATA']['smids'] = $smids;
                        $this->sendDebugEmail('PACKAGE_FP_ONLY', $debug);
                        $this->insertMessageQueuePackage($ticketId, 'PACKAGE_FP_ONLY');
                        return true;
                    }
                }
            }
        }
        return false;
    }


    // get the scheduleingMasterIds for a givent clientId, packageId and expirationId
    private function getSmids($clientId, $packageId, $expirationId)
    {

        $q = "SELECT loa.membershipNightsRemaining as nightsRemaining, loa.membershipTotalNights as totalNights, ";
        $q .= "loa.loaId as loaId, track.trackId, ";
        $q .= "GROUP_CONCAT(schedulingMasterId) AS smids FROM loa  ";
        $q .= "INNER JOIN track USING (loaId) ";
        $q .= "INNER JOIN schedulingMasterTrackRel USING (trackId)  ";
        $q .= "INNER JOIN schedulingMaster USING (schedulingMasterid) ";
        $q .= "INNER JOIN package using (packageId) ";
        $q .= "WHERE track.expirationCriteriaId =$expirationId ";
        $q .= "AND loa.clientId = $clientId ";
        $q .= "AND package.packageId=$packageId ";
        $q .= "GROUP BY packageId";
        $result = $this->query($q);
        return $result;

    }


    // mbyrnes
    // if the number of rooms and number of nights multiplied is less than or = to the number of rooms
    // in inventory, take package down
    function __runTakeDownNumRooms($offer, $ticketId, $siteOfferTable, $numNights)
    {

        $offerNumNights = $numNights * $offer['numRooms'];

        CakeLog::write("debug", "offerNumNights:$offerNumNights");

        $result = $this->getSmids($offer['clientId'], $offer['packageId'], 6);

        CakeLog::write("debug", "num results retrieved:" . count($result));

        $pids = array();
        $trackIdTakeDown_arr = array();
        foreach ($result as $k => $v) {

            $nightsRemaining = $v['loa']['nightsRemaining'];
            $totalNights = $v['loa']['totalNights'];
            $loaId = $v['loa']['loaId'];
            $smids = $v[0]['smids'];
            $trackId = $v['track']['trackId']; //123

            CakeLog::write(
                "debug",
                "nightsRemaining:$nightsRemaining-offerNumNights:$offerNumNights|totalNights:$totalNights|loaId:$loaId|smids:$smids|trackId:$trackId"
            );

            // if the nightsRemaining is less than 0
            // on hold: or there is the same trackId/loaId that was taken down
            if ($nightsRemaining - $offerNumNights <= 0) { // || (isset($trackIdTakeDown_arr[$trackId]) && $trackIdTakeDown_arr[$trackId]==$loaId)){

                // close all live Fixed Price offers and delete all future schedulingInstances / schedulingMasters
                if ($this->__runTakeDown($smids)) {

                    $trackIdTakeDown_arr[$trackId] = $loaId;

                    $this->insertMessageQueuePackage($ticketId, 'NUM_ROOMS');
                    $q = "UPDATE loa SET membershipNightsRemaining=0 WHERE loaId=$loaId";
                    CakeLog::write("debug", "takedown run $q");
                    $this->query($q);
                } else {
                    CakeLog::write("debug", "did not takedown smids: $smids");
                }
            } else {

                $q = "UPDATE loa SET membershipNightsRemaining=membershipNightsRemaining-$offerNumNights ";
                $q .= "WHERE loaId=$loaId";
                $this->query($q);

                CakeLog::write("debug", "loa decremented $q");

            }

        }

    }


    function getTicketDestStyleId($ticketId)
    {
        // europe 4
        // uk and ireland 20
        $dest = $this->query(
            "SELECT destinationId FROM ticket INNER JOIN clientLoaPackageRel USING (packageId) INNER JOIN clientDestinationRel USING (clientId) WHERE ticketId = {$ticketId}"
        );
        if (empty($dest) || !is_array($dest)) {
            return false;
        }
        $data = array();
        foreach ($dest as $d) {
            $data[] = $d['clientDestinationRel']['destinationId'];
        }
        return $data;
    }

    function getBarterOrRemit($ticketId)
    {
        $r = $this->query(
            "
            SELECT t.ticketId, tr.expirationCriteriaId
            FROM ticket t
            INNER JOIN offer o USING(offerId)
            INNER JOIN schedulingInstance i USING(schedulingInstanceId)
            INNER JOIN schedulingMaster m USING(schedulingMasterId)
            INNER JOIN schedulingMasterTrackRel r USING (schedulingMasterId)
            INNER JOIN track AS tr USING (trackId)
            WHERE t.ticketId = {$ticketId}
            "
        );
        if (empty($r) || !is_array($r)) {
            return false;
        }
        $exp = $r[0]['tr']['expirationCriteriaId'];
        if ($exp == 2 || $exp == 3) {
            return 'Remit';
        }
        if ($exp == 1 || $exp == 4 || $exp == 5 || $exp == 6) {
            return 'Barter';
        }
        return false;
    }

    function __runTakeDown($sm_ids_imp)
    {
        if (empty($sm_ids_imp) || !$sm_ids_imp) {
            return false;
        }
        $affected_rows = 0;
        $affected_rows += $this->__deleteLiveOffer($sm_ids_imp);

        $this->query("DELETE FROM schedulingInstance WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
        $affected_rows += ($this->getAffectedRows()) ? 1 : 0;

        $this->query("DELETE FROM schedulingMaster WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
        $affected_rows += ($this->getAffectedRows()) ? 1 : 0;

        $affected_rows += $this->__updateSchedulingOfferFixedPrice($sm_ids_imp);

        return ($affected_rows) ? true : false;
    }


    function __deleteLiveOffer($sm_ids_imp)
    {
        $family = $this->__deleteLiveOfferFamily($sm_ids_imp);

        $sql = 'DELETE offer o,offerLuxuryLink ol ';
        $sql .= 'FROM schedulingInstance si ';
        $sql .= 'INNER JOIN offer o USING(schedulingInstanceId) ';
        $sql .= 'INNER JOIN offerLuxuryLink ol USING(offerId) ';
        $sql .= "WHERE si.schedulingMasterId IN ($sm_ids_imp) ";
        $sql .= 'AND si.startDate > NOW()';
        $result = $this->query($sql);
        return ($this->getAffectedRows() || $family) ? 1 : 0;
    }

    // update schedulingMasterInstance with an endDate of now
    // it also updates the corresponding live offer
    function __updateSchedulingOfferFixedPrice($sm_ids_imp)
    {
        $family = $this->__updateSchedulingOfferFixedPriceFamily($sm_ids_imp);

        $sql = 'UPDATE schedulingMaster sm ';
        $sql .= 'INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId ';
        $sql .= 'INNER JOIN offer o USING(schedulingInstanceId) ';
        $sql .= 'INNER JOIN offerLuxuryLink ol USING(offerId) ';
        $sql .= 'SET si.endDate = NOW(),ol.endDate = NOW(),sm.endDate = NOW() ';
        $sql .= 'WHERE sm.offerTypeId IN(3,4) ';
        $sql .= "AND sm.schedulingMasterId IN ($sm_ids_imp) ";
        $sql .= 'AND ol.endDate > NOW()';
        $result = $this->query($sql);
        return ($this->getAffectedRows() || $family) ? 1 : 0;
    }

    function __deleteLiveOfferFamily($sm_ids_imp)
    {
        $sql = 'DELETE offer o,offerFamily ol ';
        $sql .= 'FROM schedulingInstance si ';
        $sql .= 'INNER JOIN offer o USING(schedulingInstanceId) ';
        $sql .= 'INNER JOIN offerFamily ol USING(offerId) ';
        $sql .= "WHERE si.schedulingMasterId IN ($sm_ids_imp) ";
        $sql .= 'AND si.startDate > NOW()';
        $result = $this->query($sql);
        return ($this->getAffectedRows()) ? 1 : 0;
    }

    function __updateSchedulingOfferFixedPriceFamily($sm_ids_imp)
    {
        $sql = 'UPDATE schedulingMaster sm ';
        $sql .= 'INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId ';
        $sql .= 'INNER JOIN offer o USING(schedulingInstanceId) ';
        $sql .= 'INNER JOIN offerFamily ol USING(offerId) ';
        $sql .= 'SET si.endDate = NOW(),ol.endDate = NOW(),sm.endDate = NOW() ';
        $sql .= 'WHERE sm.offerTypeId IN(3,4) ';
        $sql .= "AND sm.schedulingMasterId IN ($sm_ids_imp) ";
        $sql .= 'AND ol.endDate > NOW()';
        $result = $this->query($sql);
        return ($this->getAffectedRows()) ? 1 : 0;
    }

    function insertMessageQueuePackage($ticketId, $type, $extraData = null)
    {

        $sql = "select clpr.loaId, clpr.packageId, clpr.clientId, c.name, c.managerUsername, ";
        $sql .= "p.packageName from ticket t ";
        $sql .= "inner join clientLoaPackageRel clpr on t.packageId = clpr.packageId ";
        $sql .= "inner join package p on p.packageId = clpr.packageId ";
        $sql .= "inner join client c on c.clientId = clpr.clientId ";
        $sql .= "where t.ticketId = $ticketId";
        $result = $this->query($sql);
        CakeLog::write("debug", "insertMessageQueuePackage results: " . count($result));
        if (!empty($result)) {
            $loaId = $result[0]['clpr']['loaId'];
            $clientId = $result[0]['clpr']['clientId'];
            $packageId = $result[0]['clpr']['packageId'];
            $packageName = $result[0]['p']['packageName'];
            $clientName = $result[0]['c']['name'];
            $toUser = $result[0]['c']['managerUsername'];

            switch ($type) {

                case 'NUM_ROOMS': //mbyrnes

                    $title = "Membership Number of Nights $clientName";
                    $description = '';
                    $description .= "<a href='/loas/edit/$loaId'>$clientName</a> ";
                    $description .= "once Ticket ID <a href='/tickets/view/$ticketId'>$ticketId</a> ";
                    $description .= "is funded, it will fulfill the Membership Number of Nights Barter agreement.  ";
                    $description .= "All Fixed Price offers have been closed and all future offers have been cancelled.";
                    $modelId = $packageId;
                    $model = 'Package';
                    break;

                case 'PRICEPOINT':
                    $title = "[PRICE POINT] Maximum Number of Sales for Package [$packageName]";
                    $description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
                    $description .= "fulfill the Max Number of Sales for <a href='/clients/edit/$clientId'>$clientName</a> (Package ID# <a href='/clients/$clientId/packages/summary/$packageId'>$packageId</a>). ";
                    $description .= "All future auctions have been deleted and all fixed price offers have been closed for this package.";
                    $model = 'Package';
                    $modelId = $packageId;
                    break;
                case 'PRICEPOINT_FP_ONLY':
                    $title = "[PRICE POINT] Fixed Price offers have been stopped to prevent overselling packages for [$clientName]";
                    $description = "Once Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a> is funded, we will only need to sell one more package to ";
                    $description .= "fulfill the Max Number of Sales for Package [<a href='/clients/$clientId/packages/summary/$packageId'>$packageName</a>].  To prevent overselling, all Fixed Price ";
                    $description .= "offers running on this package have been taken down.<br /><br />";
                    $description .= "Client: <a href='/clients/edit/$clientId'>$clientName</a>";
                    $model = 'Package';
                    $modelId = $packageId;
                    break;
                case 'PACKAGE':
                    $title = "Maximum Number of Sales for Package [$packageName]";
                    $description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
                    $description .= "fulfill the Max Number of Sales for <a href='/clients/edit/$clientId'>$clientName</a> (Package ID# <a href='/clients/$clientId/packages/summary/$packageId'>$packageId</a>). ";
                    $description .= "All future auctions have been deleted and all fixed price offers have been closed for this package.";
                    $model = 'Package';
                    $modelId = $packageId;
                    break;
                case 'PACKAGE_FP_ONLY':
                    $title = "Fixed Price offers have been stopped to prevent overselling packages for [$clientName]";
                    $description = "Once Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a> is funded, we will only need to sell one more package to ";
                    $description .= "fulfill the Max Number of Sales for Package [<a href='/clients/$clientId/packages/summary/$packageId'>$packageName</a>].  To prevent overselling, all Fixed Price ";
                    $description .= "offers running on this package have been taken down.<br /><br />";
                    $description .= "Client: <a href='/clients/edit/$clientId'>$clientName</a>";
                    $model = 'Package';
                    $modelId = $packageId;
                    break;
                case 'LOA_PACKAGES':
                    $title = "Maximum Number of Sales for LOA [$clientName]";
                    $description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
                    $description .= "fulfill the Membership Number of Packages for <a href='/clients/edit/$clientId'>$clientName</a>. ";
                    $description .= "All future auctions have been deleted and all fixed price offers have been ";
                    $description .= "closed that are associated with this client's current LOA (LOA ID# <a href='/loas/edit/$loaId'>$loaId</a>).";
                    $model = 'Loa';
                    $modelId = $loaId;
                    break;
                case 'LOA_PACKAGES_FP_ONLY':
                    $title = "Fixed Price offers have been stopped to prevent overselling Membership Total Packages [$clientName]";
                    $description = "Once Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a> is funded, we will only need to sell one more package to ";
                    $description .= "fulfill the Membership Total Packages for <a href='/clients/edit/$clientId'>$clientName</a>.  To prevent overselling, all Fixed Price ";
                    $description .= "offers running on Membership Total Packages track have been taken down.<br /><br />";
                    $description .= "Package: <a href='/clients/$clientId/packages/summary/$packageId'>$packageName</a>";
                    $model = 'Loa';
                    $modelId = $loaId;
                    break;
                case 'LOA_BALANCE':
                    $title = "Membership Balance for LOA [$clientName]";
                    $description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
                    $description .= "fulfill the Membership Balance for <a href='/clients/edit/$clientId'>$clientName</a>. ";
                    $description .= "All future auctions have been deleted and all fixed price offers have been ";
                    $description .= "closed that are associated with this client's current LOA (LOA ID# <a href='/loas/edit/$loaId'>$loaId</a>).";
                    $model = 'Loa';
                    $modelId = $loaId;
                    break;
                case 'RETAIL_VALUE':
                    $title = "Retail Value Credit Balance for LOA [$clientName]";
                    $model = 'Loa';
                    $modelId = $loaId;
                    $description = "$clientName is nearing a 0 balance of Retail Value Credit for LOA ID (<a href='/loas/edit/$loaId'>$loaId</a>).  To prevent overselling, all live and future scheduled offers for the following package(s) have been cancelled: \n\n<br /><br />";
                    foreach ($extraData as $pId) {
                        $description .= "Package: <a href='/clients/$clientId/packages/summary/$pId'>$pId</a>\n<br />";
                    }

            }

            $title = Sanitize::Escape($title);
            $description = Sanitize::Escape($description);
            $sql = "CALL insertQueueMessage('$toUser', '$title', '$description', '$model', $modelId, 3)";
            $this->query($sql);

        }
    }

    /**
     * @param $ticketId
     * @return bool
     */
    public function isInternational($ticketId)
    {
        $this->recursive = false;
        $ticket = $this->read('Ticket.tldId', $ticketId);
        if (isset($ticket['Ticket']['tldId'])) {
            return ($ticket['Ticket']['tldId'] > 1);
        } else {
            return false;
        }
    }

    /**
     * @param $ticketId
     * @return bool
     */
    public function isForeignCurrencyTicket($ticketId)
    {
        $this->recursive = false;
        $ticket = $this->read('Ticket.useTldCurrency', $ticketId);
        if (isset($ticket['Ticket']['useTldCurrency'])) {
            return ($ticket['Ticket']['useTldCurrency'] == 1);
        } else {
            return false;
        }
    }

    public function getUserPaymentSettingId($ticketId)
    {
        $sql = "SELECT userPaymentSettingId FROM ticket WHERE ticketId=?";
        $params = array($ticketId);

        $result = $this->query($sql, $params);
        $userPaymentSettingId = isset($result[0]['ticket']['userPaymentSettingId']) ? $result[0]['ticket']['userPaymentSettingId'] : false;

        return $userPaymentSettingId;
    }

    public function validateRegisteredUser($userId = null)
    {
        $sql = "SELECT userId FROM userSiteExtended WHERE userId = ?";
        $result = $this->query($sql, array($userId));
        if (empty($result)){
            return false;
        }
        return true;
    }
}
