<?php
App::import('Vendor', 'nusoap_client/lib/nusoap');
require(APP . '/vendors/pp/Processor.class.php');
class PaymentDetailsController extends AppController
{
    public $name = 'PaymentDetails';
    public  $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
    public  $uses = array(
        'PaymentDetail',
        'PaymentProcessor',
        'Ticket',
        'UserPaymentSetting',
        'PpvNotice',
        'Country',
        'CountryBilling',
        'Track',
        'TrackDetail',
        'User',
        'creditTracking',
        'PromoTicketRel',
        'Promo',
        'PromoCode',
        'PaymentType',
        'PaymentDetail',
        'Readonly',
        'PgPayment'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        $currentUser = $this->LdapAuth->user();
        $this->canSave = false;
        if (in_array('Accounting', $currentUser['LdapUser']['groups']) || in_array(
                'concierge',
                $currentUser['LdapUser']['groups']
            ) || in_array('Geeks', $currentUser['LdapUser']['groups'])
        ) {
            $this->canSave = true;
        }

        $this->set('canSave', $this->canSave);
    }

    public function index()
    {
        $this->PaymentDetail->recursive = 0;
        $this->set('paymentDetails', $this->paginate());
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Payment Detail Id.', true), 'default', array(), 'error');
        }
        $arr = $this->PaymentDetail->readPaymentDetail($id);
        $this->set('paymentDetail', $arr[0]);

    }

    public function viewPg($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Payment Detail Id.', true), 'default', array(), 'error');
        }

        $arr = array();
        $pgPaymentResult = $this->PgPayment->readPgPayment($id);

        if ($pgPaymentResult['PgPayment']['paymentTypeId']==2 || $pgPaymentResult['PgPayment']['paymentTypeId']==3){//Credit on File, Promo or Gift Cert
            $arr = $pgPaymentResult;
            $arr['PgPayment']['paymentUSD'] = $pgPaymentResult['PgPayment']['paymentUSD'];
            if ($pgPaymentResult['PgPayment']['paymentTypeId']==2) // Promo or Gift Cert
                $arr['PaymentType']['paymentTypeName'] = 'Gift Certificate/Promo';
            else
                $arr['PaymentType']['paymentTypeName'] = 'Credit on File';
        }
        else {
            $this->PgPayment->recursive = 1;

            //'fields' need to be set first due to the ordering of joins
            $params =  array(
                'conditions' => array('PgPayment.pgPaymentId' => $id),
                'fields' => array('PgPayment.*','PgBookingAuthAttempt.ll2CreditCardAuthId', 'PgPaymentDetailCreditCard.*', 'PgBooking.*', 'PaymentType.*', 'Ll2CreditCardAuth.*' )
            );

            $params['joins'] = array(
                array('table' => 'pgPaymentDetailCreditCard',
                    'alias' => 'PgPaymentDetailCreditCard',
                    'type' => 'inner',
                    'conditions' => array(
                        'PgPaymentDetailCreditCard.pgPaymentId = PgPayment.pgPaymentId'
                    )
                ),
                array('table' => 'pgBookingAuthAttempt',
                    'alias' => 'PgBookingAuthAttempt',
                    'type' => 'inner',
                    'conditions' => array(
                        'PgBookingAuthAttempt.pgBookingAuthAttemptId = PgPaymentDetailCreditCard.pgBookingAuthAttemptId'
                    )
                ),
                array('table' => 'll2CreditCardAuth',
                    'alias' => 'Ll2CreditCardAuth',
                    'type' => 'inner',
                    'conditions' => array(
                        'Ll2CreditCardAuth.ll2CreditCardAuthId = PgBookingAuthAttempt.ll2CreditCardAuthId'
                    )
                ),
            );

            $options['conditions'] = array(
                 'PgPayment.pgPaymentId' => $id); //array of conditions

            $arr = $this->PgPayment->find('first',$params);
            $arr['PgBooking']['validCard'] = $this->getValidCcOnFile($arr['PgBooking']['userId']);
        }

        $this->set('paymentDetail', $arr);

    }
    public function void()
    {

        $currentUser = $this->LdapAuth->user();
        if (!in_array('Accounting', $currentUser['LdapUser']['groups']) && !in_array(
                'Geeks',
                $currentUser['LdapUser']['groups']
            )
        ) {
            $this->Session->setFlash(__('You do not have permission to void payments.', true));
            $this->redirect(array('controller' => 'tickets', 'action' => 'view', $this->params['ticketId']));
        }

        if (isset($this->params['url']['v']) && intval($this->params['url']['v']) > 0) {
            if (isset($this->params['url']['cnf']) && $this->params['url']['cnf'] == '1') {
                $data = array('PaymentDetail' => array());
                $data['PaymentDetail']['paymentDetailId'] = $this->params['url']['v'];
                $data['PaymentDetail']['isSuccessfulCharge'] = 0;
                $data['PaymentDetail']['ppApprovalText'] = 'Voided';
                $data['PaymentDetail']['isVoided'] = 1;
                $data['PaymentDetail']['voidDate'] = date("Y-m-d H:i:s");
                $data['PaymentDetail']['voidInitials'] = $currentUser['LdapUser']['samaccountname'];
                $this->PaymentDetail->save($data);
                $this->Session->setFlash(__('Payment Has Been Voided', true));
            } else {
                $this->set('confirm', $this->params['url']['v']);
            }
        }

        $params = array(
            'conditions' => array(
                'Ticket.ticketId' => $this->params['ticketId'],
            ),
            'contain' => array(
                'PaymentDetail'
            ),
        );

        $ticket = $this->PaymentDetail->Ticket->find('first', $params);
        $this->set('ticket', $ticket);
    }

    public function add()
    {
        if (!empty($this->data)) {
            switch ($this->data['PaymentDetail']['paymentTypeId']) {
                case "2":
                    $this->data['PaymentDetail']['paymentProcessorId'] = 6;
                    break;
                case "3":
                    $this->data['PaymentDetail']['paymentProcessorId'] = 6;
                    break;
                case "4":
                    $this->data['PaymentDetail']['paymentProcessorId'] = 7;
                    break;
                default:
                    break;
            }

            $webservice_live_url = Configure::read("Url.Ws") . '/web_service_tickets?wsdl';
            $webservice_live_method_name = 'processPaymentTicket';
            $webservice_live_method_param = 'in0';

            $this->User->recursive = -1;
            $userData = @$this->User->read(null, $this->data['PaymentDetail']['userId']);
            $ticketId = @$this->data['PaymentDetail']['ticketId'];

            $data = array();
            $data['userId'] = $this->data['userId'];
            $data['ticketId'] = $this->data['ticketId'];
            $data['paymentProcessorId'] = $this->data['PaymentDetail']['paymentProcessorId'];
            $data['paymentTypeId'] = $this->data['PaymentDetail']['paymentTypeId'];
            $data['paymentAmount'] = $this->data['PaymentDetail']['paymentAmount'];
            $data['initials'] = $this->data['PaymentDetail']['initials'];
            $data['ppTransactionId'] = $this->data['PaymentDetail']['ppTransactionId'];
            $data['firstName'] = $userData['User']['firstName'];
            $data['lastName'] = $userData['User']['lastName'];

            $data['autoCharge'] = 0;
            $data['saveUps'] = 0;
            $data['toolboxManualCharge'] = 'toolbox';

            if (!$data['initials']) {
                $data['initials'] = 'MANUALTOOLBOX';
            }

            $data['zAuthHashKey'] = md5(
                'L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']
            );

            $data_json_encoded = json_encode($data);
            $soap_client = new nusoap_client($webservice_live_url, true);

            if ($this->data['PaymentDetail']['paymentProcessorId'] == 5) {
                // for wire transfers only
                // --------------------------------------------------------
                $ticketId = $this->data['PaymentDetail']['ticketId'];

                $this->data['PaymentDetail']['ccType'] = 'WT';
                $this->data['PaymentDetail']['userPaymentSettingId'] = '';
                $this->data['PaymentDetail']['isSuccessfulCharge'] = 1;
                $this->data['PaymentDetail']['autoProcessed'] = 0;
                $this->data['PaymentDetail']['ppResponseDate'] = date('Y-m-d H:i:s', strtotime('now'));
                $this->data['PaymentDetail']['ppBillingAmount'] = $this->data['PaymentDetail']['paymentAmount'];
                $this->data['PaymentDetail']['ppCardNumLastFour'] = 'WIRE';
                $this->data['PaymentDetail']['ppExpMonth'] = 'WT';
                $this->data['PaymentDetail']['ppExpYear'] = 'WIRE';
                $this->data['PaymentDetail']['ppFirstName'] = $userData['User']['firstName'];
                $this->data['PaymentDetail']['ppLastName'] = $userData['User']['lastName'];
                $this->data['PaymentDetail']['ppBillingAddress1'] = 'WIRE';
                $this->data['PaymentDetail']['ppBillingCity'] = 'WIRE';
                $this->data['PaymentDetail']['ppBillingState'] = 'WIRE';
                $this->data['PaymentDetail']['ppBillingZip'] = 'WIRE';
                $this->data['PaymentDetail']['ppBillingCountry'] = 'WIRE';

                if ($this->PaymentDetail->save($this->data['PaymentDetail'])) {
                    // update ticket status to FUNDED
                    // ---------------------------------------------------------------------------
                    $ticketStatusChange = array();
                    $ticketStatusChange['ticketId'] = $ticketId;
                    $ticketStatusChange['ticketStatusId'] = 5;
                    $this->Ticket->save($ticketStatusChange);

                    // allocate revenue to loa and tracks
                    // ---------------------------------------------------------------------------
                    $tracks = $this->TrackDetail->getTrackRecord($ticketId);
                    if (!empty($tracks)) {
                        foreach ($tracks as $track) {
                            // track detail stuff and allocation
                            // ---------------------------------------------------------------------------
                            $trackDetailExists = $this->TrackDetail->findExistingTrackTicket(
                                $track['trackId'],
                                $ticketId
                            );
                            if (!$trackDetailExists) {
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
                                $new_track_detail = $this->TrackDetail->getNewTrackDetailRecord($track, $ticketId);
                                if ($new_track_detail) {
                                    $this->TrackDetail->create();
                                    $this->TrackDetail->save($new_track_detail);
                                }
                            }
                        }
                    }
                }
                // --------------------------------------------------------
            } elseif ($this->data['PaymentDetail']['paymentProcessorId'] == 6) {
                // Credit on file or gift
                $paymentResponse = $soap_client->call(
                    $webservice_live_method_name,
                    array($webservice_live_method_param => $data_json_encoded)
                );
            } elseif ($this->data['PaymentDetail']['paymentProcessorId'] == 7) {
                // Promo codes

                $this->loadModel('PromoCode');
                $code = $this->PromoCode->findBypromoCode($this->data['PaymentDetail']['ppTransactionId']);

                // ticket 3446 - minimum purchase restriction
                $minimumPurchase = intval($code['Promo'][0]['minPurchaseAmount']);
                $ticketLookup = $this->Ticket->query(
                    'SELECT * FROM ticket WHERE ticketId = ?',
                    array($this->data['ticketId'])
                );
                $ticketPrice = $ticketLookup[0]['ticket']['billingPrice'];

                if ($ticketPrice < $minimumPurchase) {
                    $this->Session->setFlash(
                        __(
                            $code['PromoCode']['promoCode'] . ' requires a minimum purchase of $' . $minimumPurchase,
                            true
                        )
                    );
                    $this->redirect("/tickets/" . $this->data['ticketId'] . "/payment_details/add");
                } else {

                    $this->PaymentDetail->Ticket->PromoTicketRel->create();
                    $this->PaymentDetail->Ticket->PromoTicketRel->save(
                        array(
                            'ticketId' => $this->data['ticketId'],
                            'userId' => $this->data['userId'],
                            'promoCodeId' => $code['PromoCode']['promoCodeId']
                        )
                    );

                    $this->redirect("/tickets/" . $this->data['ticketId'] . "/payment_details/add");
                    $paymentResponse = "CHARGE_SUCCESS";
                }


            } elseif (
                $this->data['PaymentDetail']['paymentProcessorId'] == 1
                || $this->data['PaymentDetail']['paymentProcessorId'] == 3
                || $this->data['PaymentDetail']['paymentProcessorId'] == 8
            ) {
                // Credit card
                $usingNewCard = 0;
                $saveNewCard = 0;

                if (isset($this->data['UserPaymentSetting']['useNewCard'])) {
                    unset($this->data['UserPaymentSetting']['useNewCard']);
                    $usingNewCard = 1;
                }
                if (isset($this->data['UserPaymentSetting']['save'])) {
                    unset($this->data['UserPaymentSetting']['save']);
                    $saveNewCard = 1;
                }

                $data['saveUps'] = $saveNewCard;
                $badPaymentRequest = false;

                if ($usingNewCard) {
                    $data['userPaymentSetting'] = $this->data['UserPaymentSetting'];

                    $expMonth = str_pad($this->data['UserPaymentSetting']['expMonth'], 2, 0, STR_PAD_LEFT);
                    $expYear = substr($this->data['UserPaymentSetting']['expYear'], -2);
                    $expirationDate = $expMonth . $expYear;

                    $data['userPaymentSetting']['ccType'] =
                        $this->UserPaymentSetting->getCcType($data['userPaymentSetting']['ccNumber']);

                    $data['userPaymentSetting']['ccToken'] =
                        $this->UserPaymentSetting->tokenizeCcNum(
                            $data['userPaymentSetting']['ccNumber'],
                            $expirationDate
                        );
                    unset($data['userPaymentSetting']['ccNumber']);

                    $data_json_encoded = json_encode($data);
                } elseif ($this->data['PaymentDetail']['userPaymentSettingId']) {
                    $data['userPaymentSettingId'] = $this->data['PaymentDetail']['userPaymentSettingId'];
                    $data_json_encoded = json_encode($data);
                } else {
                    $badPaymentRequest = true;
                }

                if (!$badPaymentRequest) {
                    $paymentResponse = $soap_client->call(
                        $webservice_live_method_name,
                        array(
                            $webservice_live_method_param => $data_json_encoded
                        )
                    );

                    $this->Ticket->recursive = -1;
                    $ticketRead = $this->Ticket->read(null, $ticketId);
                    if (trim($paymentResponse) == 'CHARGE_SUCCESS') {
                        if (in_array($ticketRead['Ticket']['offerId'], array(3, 4))) {
                            $ticketData['ticketId'] = $ticketId;
                            $webservice_live_method_name = 'autoSendXnetDatesConfirmedOnlyProperty';
                            $webservice_live_method_param = 'in0';
                            $data_json_encoded = json_encode($ticketData);
                            $soap_client = new nusoap_client($webservice_live_url, true);
                            $soap_client->call(
                                $webservice_live_method_name,
                                array($webservice_live_method_param => $data_json_encoded)
                            );
                        }

                        $error = $paymentResponse;
                    } else {
                        if (in_array($ticketRead['Ticket']['offerId'], array(3, 4))) {
                            $ticketData['ticketId'] = $ticketId;
                            $webservice_live_method_name = 'autoSendXnetCCDeclined';
                            $webservice_live_method_param = 'in0';
                            $data_json_encoded = json_encode($ticketData);
                            $soap_client = new nusoap_client($webservice_live_url, true);
                            $soap_client->call(
                                $webservice_live_method_name,
                                array($webservice_live_method_param => $data_json_encoded)
                            );
                        }
                    }
                } else {
                    $error = "BAD_PAYMENT";
                }
            } else {
                $error = "NO_ACCT";
            }

            if (isset($error)) {
                CakeLog::write("debug", var_export(array("WEB SERVICE TICKETS: ", $error), 1));
                echo $error;
            } else {
                echo $paymentResponse;
            }

            exit;
        }

        // NO POST BELOW -- GRAB DATA

        //$this->PaymentDetail->Ticket->recursive = 0;

        $params = array(
            'conditions' => array(
                'Ticket.ticketId' => $this->params['ticketId'],
            ),
            'contain' => array(
                'Package',
                'PaymentDetail',
                'User' => array(
                    'UserPaymentSetting'
                ),
            ),
        );

        $ticket = $this->PaymentDetail->Ticket->find('first', $params);

        $useTldCurrency = isset($ticket['Ticket']['useTldCurrency']) ? $ticket['Ticket']['useTldCurrency'] : 0;
        $billingPrice = ($useTldCurrency == 1) ? $ticket['Ticket']['billingPriceTld'] : $ticket['Ticket']['billingPrice'];
        $this->set('useTldCurrency', $useTldCurrency);
        $this->set('billingPrice', $billingPrice);
        $this->set('processingFee', $this->Ticket->getFeeByTicket($ticket['Ticket']['ticketId']));
        $this->set('currencyName', $this->Ticket->getCurrencyNameByTicketId($ticket['Ticket']['ticketId']));
        $this->set('currencySymbol', $this->Ticket->getCurrencySymbolByTicketId($ticket['Ticket']['ticketId']));

        // Ticket 1002
        // Only apply CoF for credits BEFORE ticket created. Always show CoF for manual charges.
        $ticket['UserPromo'] = $this->Ticket->getPromoGcCofData(
            $ticket['Ticket']['ticketId'],
            $billingPrice,
            0,
            true
        );

        $paymentProcessors = $this->PaymentDetail->PaymentProcessor->find(
            'list',
            array('conditions' => array('sites LIKE' => '%' . $ticket['Ticket']['siteId'] . '%'))
        );

        if ($useTldCurrency == 1) {
            $paymentProcessors = array(
                '8' => 'PAYPAL_i18n'
            );
        }
        $paymentTypeIds = $this->PaymentDetail->PaymentType->find('list');

        $fee = $this->Ticket->getFeeByTicket($ticket['Ticket']['ticketId']);

        // Ajax to get payment in add.ctp
        if (isset($this->params['url']['get_payment']) && $this->params['url']['get_payment'] != "" && isset($ticket['UserPromo'][$this->params['url']['get_payment']]['totalAmountOff'])) {
            $payment_amt = $ticket['UserPromo'][$this->params['url']['get_payment']]['totalAmountOff'];
        } else {
            $payment_amt = $ticket['UserPromo']['final_price_actual'];
        }

        if ($payment_amt < 0) {
            $payment_amt = 0;
        }

        if (isset($this->params['url']['get_payment'])) {
            echo json_encode(
                array(
                    'payment_amt' => $payment_amt,
                    'total_payments' => $ticket['UserPromo']['payments'],
                    'balance' => $ticket['UserPromo']['final_price_actual']
                )
            );
            exit;
        }

        $selectExpMonth = array();
        for ($i = 1; $i < 13; $i++) {
            $se_m = str_pad($i, 2, '0', STR_PAD_LEFT);
            $selectExpMonth[] = $se_m;
        }
        $selectExpYear = array();
        $yearPlusSeven = date('Y', strtotime("+7 YEAR"));
        for ($i = date('Y'); $i <= $yearPlusSeven; $i++) {
            $selectExpYear[] = $i;
        }

        if ($this->isSecuredUser($this->user['LdapUser']['username'])) {
            $allowViewCCFull = true;
        } else {
            $allowViewCCFull = false;
        }

        if (isset($ticket['UserPromo']['Promo']) && $ticket['UserPromo']['Promo']['applied'] == 1) {
            unset($paymentTypeIds[4]);
        }

        $this->set('allowViewCCFull', $allowViewCCFull);
        $this->set('ticket', $ticket);
        $this->set('countries', $this->CountryBilling->getList());
        $this->set('selectExpMonth', $selectExpMonth);
        $this->set('selectExpYear', $selectExpYear);
        $this->set(
            'userPaymentSetting',
            (isset($ticket['User']['UserPaymentSetting']) ? $ticket['User']['UserPaymentSetting'] : array())
        );
        $this->set('paymentTypeIds', $paymentTypeIds);
        $this->set('paymentProcessorIds', $paymentProcessors);
        $this->set('initials_user', $this->user['LdapUser']['username']);
        $this->set('nocollapse', 1);
        $this->set('userPaymentSettingId', $this->Ticket->getUserPaymentSettingId($ticket['Ticket']['ticketId']));

        if (isset($this->params['url']['payments_applied'])) {
            $this->render("payments_applied", "ajax");
        } elseif (isset($this->params['url']['existing_cards'])) {
            $this->render("existing_cards", "ajax");
        }
    }
    public function getValidCcOnFile($userId)
    {
        $ups = $this->Readonly->query(
            "select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc"
        );

        $year_now = date('Y');
        $month_now = date('m');
        if (empty($ups)) {
            return 'NONE';
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
        return ($found_valid_cc) ? $v['UserPaymentSetting']['ccType'] . '-' . substr(
                $v['UserPaymentSetting']['ccToken'],
                -4,
                4
            ) : 'EXPIRED';
    }
    /**
     * @param $userPaymentSettingId
     */
    public function detokenize($userPaymentSettingId)
    {
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->autoRender = false;

        $ccToken = 0;

        $params = array(
            'callbacks' => false,
            'conditions' => array('UserPaymentSetting.userPaymentSettingId' => $userPaymentSettingId),
            'fields' => array('UserPaymentSetting.ccToken')
        );
        $userPaymentSetting = $this->UserPaymentSetting->find('first', $params);
        if ($this->isSecuredUser($this->user['LdapUser']['username'])) {
            $ccToken = $this->UserPaymentSetting->detokenizeCcNum($userPaymentSetting['UserPaymentSetting']['ccToken']);
        }

        echo $ccToken;
    }
}
