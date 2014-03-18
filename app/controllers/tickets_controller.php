<?php
App::import('Vendor', 'nusoap_client/lib/nusoap');
class TicketsController extends AppController
{
    var $name = 'Tickets';
    var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
    const PASSCODE_STATUS_PROMO = 'P';
    const PASSCODE_STATUS_GC = 'G';
    /**
     * @var LltgServiceHelper $LltgServiceHelper
     */
    public $LltgServiceHelper;
   // public $Translation;

    var $uses = array(
        'Ticket',
        'OfferType',
        'Format',
        'User',
        'ClientLoaPackageRel',
        'Track',
        'TrackDetail',
        'Offer',
        'Loa',
        'Client',
        'OfferLuxuryLink',
        'OfferFamily',
        'Reservation',
        'PromoTicketRel',
        'Promo',
        'PromoCode',
        'PaymentType',
        'Readonly',
        'ReservationPreferDate',
        'ReservationPreferDateFromHotel'
    );

    var $components = array(
        'LltgServiceHelper',
        'Translation'
    );

    function index()
    {
        // Readonly db config
        $this->Ticket->useReadonlyDb();
        $this->Ticket->TicketStatus->useReadonlyDb();
        $this->Ticket->PromoTicketRel->useReadonlyDb();
        $this->Ticket->Reservation->useReadonlyDb();

        // set search criteria from form post or set defaults
        $form = $this->params['form'];
        $named = $this->params['named'];

        // ajaxed paginated form elements come in via params['named']
        if (empty($form) && !empty($named)) {
            $form = $named;
            $this->params['form'] = $this->params['named'];
        }

        // export take out limit
        $csv_export = isset($this->params['named']['csv_export']) ? $this->params['named']['csv_export'] : false;

        // set values and set defaults
        $s_ticket_id = isset($form['s_ticket_id']) ? $form['s_ticket_id'] : '';
        $s_offer_id = isset($form['s_offer_id']) ? $form['s_offer_id'] : '';
        $s_user_id = isset($form['s_user_id']) ? $form['s_user_id'] : '';
        $s_format_id = isset($form['s_format_id']) ? $form['s_format_id'] : '';
        $s_site_id = isset($form['s_site_id']) ? $form['s_site_id'] : '';
        $s_tld_id = isset($form['s_tld_id']) ? $form['s_tld_id'] : '';
        $s_client_id = isset($form['s_client_id']) ? $form['s_client_id'] : '';
        $s_bid_id = isset($form['s_bid_id']) ? $form['s_bid_id'] : '';
        $s_quick_link = isset($form['s_quick_link']) ? $form['s_quick_link'] : '';
        $s_package_id = isset($form['s_package_id']) ? $form['s_package_id'] : '';
        $s_price_point_id = isset($form['s_price_point_id']) ? $form['s_price_point_id'] : '';
        $s_promo_code = isset($form['s_promo_code']) ? $form['s_promo_code'] : '';
        $s_offer_type_id = isset($form['s_offer_type_id']) ? $form['s_offer_type_id'] : 0;
        $s_ticket_status_id = isset($form['s_ticket_status_id']) ? $form['s_ticket_status_id'] : 0;
        $s_res_confirmation_num = isset($form['s_res_confirmation_num']) ? $form['s_res_confirmation_num'] : '';
        $s_res_check_in_date = isset($form['s_res_check_in_date']) ? $form['s_res_check_in_date'] : '';
        $s_has_promo = isset($form['s_has_promo']) ? $form['s_has_promo'] : '';
        $s_manual_ticket = isset($form['s_manual_ticket']) ? $form['s_manual_ticket'] : '';
        $s_start_y = isset($form['s_start_y']) ? $form['s_start_y'] : date('Y');
        $s_start_m = isset($form['s_start_m']) ? $form['s_start_m'] : date('m');
        $s_start_d = isset($form['s_start_d']) ? $form['s_start_d'] : date('d');
        $s_end_y = isset($form['s_end_y']) ? $form['s_end_y'] : date('Y');
        $s_end_m = isset($form['s_end_m']) ? $form['s_end_m'] : date('m');
        $s_end_d = isset($form['s_end_d']) ? $form['s_end_d'] : date('d');

        if (isset($_GET['searchClientId'])) {
            $s_client_id = $_GET['searchClientId'];
        }

        if (isset($_GET['searchUserId'])) {
            $s_user_id = $_GET['searchUserId'];
        }

        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            if (is_numeric($query)) {
                $s_ticket_id = $_GET['query'];
            }
        }

        $allowed_query_keys = array(
            's_ticket_status_id',
            's_format_id',
            's_offer_type_id',
            's_quick_link',
            's_site_id',
            's_tld_id'
        );
        foreach ($this->params['url'] as $key => $value) {
            if (in_array($key, $allowed_query_keys)) {
                $$key = $value;
                $this->params['form'][$key] = $value;
            }
        }

        if ($s_res_check_in_date || $s_res_confirmation_num) {
            $s_has_reservation = true;
        } else {
            $s_has_reservation = false;
        }

        // use these dates in the sql for date range search
        $s_start_date = $s_start_y . '-' . $s_start_m . '-' . $s_start_d . ' 00:00:00';
        $s_end_date = $s_end_y . '-' . $s_end_m . '-' . $s_end_d . ' 23:59:59';

        $this->paginate = array(
            'fields' => array(
                'Ticket.ticketId',
                'Ticket.offerTypeId',
                'Ticket.created',
                'Ticket.bidId',
                'Ticket.tldId',
                'Ticket.offerId',
                'Ticket.userId',
                'TicketStatus.ticketStatusName',
                'Ticket.packageId',
                'Ticket.userFirstName',
                'Ticket.userLastName',
                'Ticket.packageId',
                'Ticket.billingPrice',
                'Ticket.billingPriceTld',
                'Ticket.numNights',
                'Ticket.formatId',
                'Ticket.ticketNotes',
                'Ticket.siteId',
                'Ticket.requestArrival',
                'Ticket.requestDeparture',
                'MAX(PpvNotice.emailSentDatetime) as emailSentDatetime',
                'MAX(ReservationPreferDate.arrivalDate) as arrivalDate',
                'MAX(ReservationPreferDate.departureDate) as departureDate',
                'COUNT(PpvNotice.ppvNoticeId) AS rescount'
            ),
            'contain' => array('TicketStatus'),
            'order' => array(
                'Ticket.ticketId' => 'desc'
            ),
            'limit' => 50,
            'joins' => array(
                array(
                    'table' => 'ppvNotice',
                    'alias' => 'PpvNotice',
                    'type' => 'left',
                    'conditions' => array(
                        'PpvNotice.ticketId = Ticket.ticketId',
                        'PpvNotice.ppvNoticeTypeId IN (2,10,24,25)'
                    )
                ),
                array(
                    'table' => 'reservationPreferDate',
                    'alias' => 'ReservationPreferDate',
                    'type' => 'left',
                    'conditions' => array(
                        'ReservationPreferDate.ticketId = Ticket.ticketId',
                        'ReservationPreferDateTypeId' => 1
                    )
                )
            ),
            'group' => array('Ticket.ticketId')
        );

        if ($csv_export) {
            $this->paginate['limit'] = 10000;
        }

        $single_search = true;
        $single_search_override = false;
        // if search via ticket id, offer id, or user id, then dont use other search conditions
        if ($s_ticket_id) {
            $this->paginate['conditions']['Ticket.ticketId'] = $s_ticket_id;
        } elseif (isset($s_quick_link) && !empty($s_quick_link)) {
            switch ($s_quick_link) {
                case 1:
                    $this->paginate['conditions']['Ticket.formatId'] = 1;
                    $this->paginate['conditions']['Ticket.ticketStatusId'] = 3;
                    $this->paginate['order'] = array('emailSentDatetime' => 'desc');
                    break;
                case 2:
                    $this->paginate['conditions']['Ticket.formatId'] = 1;
                    $this->paginate['conditions']['Ticket.ticketStatusId <> '] = 4;
                    $this->paginate['group'] = array('Ticket.ticketId HAVING rescount > 1');
                    break;
                case 3:
                    $this->paginate['conditions']['Ticket.formatId'] = 2;
                    $this->paginate['conditions']['Ticket.ticketStatusId <> '] = 4;
                    $this->paginate['group'] = array('Ticket.ticketId HAVING rescount > 1');
                    break;
                case 4:
                    $this->paginate['conditions']['not'] = array(
                        'Ticket.manualTicketInitials' => null,
                        'Ticket.ticketStatusId' => array(4, 6, 7, 8, 17, 18)
                    );
                    break;
            }
            $single_search_override = true;
        } elseif ($s_user_id) {
            $this->paginate['conditions']['Ticket.userId'] = $s_user_id;
        } elseif ($s_bid_id) {
            $this->paginate['conditions']['Ticket.bidId'] = $s_bid_id;
        } elseif ($s_offer_id) {
            $this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;
        } elseif ($s_client_id) {
            $this->paginate['joins'][] =
                array(
                    'table' => 'clientLoaPackageRel',
                    'alias' => 'ClientLoaPackageRel',
                    'type' => 'inner',
                    'conditions' => array('ClientLoaPackageRel.packageId = Ticket.packageId')
                );

            $this->paginate['joins'][] =
                array(
                    'table' => 'client',
                    'alias' => 'Client',
                    'type' => 'inner',
                    'conditions' => array(
                        'Client.clientId = ClientLoaPackageRel.clientId',
                        'Client.clientId' => $s_client_id
                    )
                );
            $this->paginate['group'] = array('Ticket.ticketId');
        } elseif ($s_package_id) {
            $this->paginate['conditions']['Ticket.packageId'] = $s_package_id;
        } elseif ($s_promo_code) {
            $promoCodeResult = $this->PromoCode->findBypromoCode($s_promo_code);
            $s_promo_code_id = $promoCodeResult['PromoCode']['promoCodeId'];
            $this->paginate['contain'][] = 'PromoTicketRel';
            $this->paginate['group'] = array('Ticket.ticketId');
            $this->paginate['joins'][] =
                array(
                    'table' => 'promoTicketRel',
                    'alias' => 'PromoTicketRel',
                    'type' => 'inner',
                    'conditions' => array('PromoTicketRel.ticketId = Ticket.ticketId')
                );
            $this->paginate['conditions']['PromoTicketRel.promoCodeId'] = $s_promo_code_id;
        } else {
            $single_search = false;
            $this->paginate['conditions']['Ticket.created BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);
            if ($s_offer_type_id) {
                $this->paginate['conditions']['Ticket.offerTypeId'] = $s_offer_type_id;
            }
            if ($s_format_id) {
                $this->paginate['conditions']['Ticket.formatId'] = $s_format_id;
            }
            if ($s_site_id) {
                $this->paginate['conditions']['Ticket.siteId'] = $s_site_id;
            }
            if ($s_tld_id) {
                $this->paginate['conditions']['Ticket.tldId'] = $s_tld_id;
            }
            if ($s_offer_id) {
                $this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;
            }
            if ($s_price_point_id) {
                $this->paginate['conditions']['PricePoint.pricePointId'] = $s_price_point_id;
                $this->paginate['joins'][] = array(
                    'table' => 'pricePoint',
                    'alias' => 'PricePoint',
                    'type' => 'left',
                    'conditions' => 'Ticket.packageId = PricePoint.packageId'
                );
            }
            if ($s_ticket_status_id) {
                $this->paginate['conditions']['Ticket.ticketStatusId'] = $s_ticket_status_id;
                if ($s_ticket_status_id == 3) {
                    // jwoods - Jim asked that the tickets remain sorted by ticketId
                    // $this->paginate['order'] = array('emailSentDatetime' => 'desc');
                }
            }
            if ($s_has_reservation) {
                $this->paginate['contain'][] = 'Reservation';
                $this->paginate['group'] = array('Ticket.ticketId');
                $this->paginate['conditions']['Reservation.reservationId > '] = 0;
                if ($s_res_confirmation_num) {
                    $this->paginate['conditions']['Reservation.reservationConfirmNum'] = trim($s_res_confirmation_num);
                    $single_search = true;
                    unset($this->paginate['conditions']['Ticket.created BETWEEN ? AND ?']);
                }
                if ($s_res_check_in_date) {
                    $this->paginate['conditions']['ReservationPreferDate.arrivalDate BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);
                    unset($this->paginate['conditions']['Ticket.created BETWEEN ? AND ?']);
                }
            }
            if ($s_has_promo) {
                $this->paginate['contain'][] = 'PromoTicketRel';
                $this->paginate['group'] = array('Ticket.ticketId');
                $this->paginate['joins'][] =
                    array(
                        'table' => 'promoTicketRel',
                        'alias' => 'PromoTicketRel',
                        'type' => 'inner',
                        'conditions' => array('PromoTicketRel.ticketId = Ticket.ticketId')
                    );
                $this->paginate['conditions']['PromoTicketRel.promoCodeId > '] = 0;
            }
            if ($s_manual_ticket) {
                $this->paginate['conditions'][] = array('Ticket.manualTicketInitials IS NOT NULL');
            }
        }

        // allow package/client/user/pricePoint to use date and status
        if ($s_package_id || $s_client_id || $s_user_id || $s_price_point_id) {
            $single_search = false;
            if ($s_ticket_status_id) {
                $this->paginate['conditions']['Ticket.ticketStatusId'] = $s_ticket_status_id;
            }
            if ($s_offer_type_id) {
                $this->paginate['conditions']['Ticket.offerTypeId'] = $s_offer_type_id;
            }
            if ($s_format_id) {
                $this->paginate['conditions']['Ticket.formatId'] = $s_format_id;
            }
            if ($s_site_id) {
                $this->paginate['conditions']['Ticket.siteId'] = $s_site_id;
            }
            if ($s_tld_id) {
                $this->paginate['conditions']['Ticket.tldId'] = $s_tld_id;
            }
        }

        if (!$single_search) {
            $s_ticket_id = $s_offer_id = $s_bid_id = $s_res_confirmation_num = null;
        } else {
            $s_res_check_in_date = $s_offer_type_id = $s_has_promo = $s_manual_ticket = null;
            if (!$single_search_override) {
                $s_ticket_status_id = $s_format_id = $s_site_id = $s_tld_id = null;
            }
            $s_start_y = $s_end_y = date('Y');
            $s_start_m = $s_end_m = date('m');
            $s_start_d = $s_end_d = date('d');
        }

        $this->set('s_ticket_id', $s_ticket_id);
        $this->set('s_offer_id', $s_offer_id);
        $this->set('s_user_id', $s_user_id);
        $this->set('s_client_id', $s_client_id);
        $this->set('s_bid_id', $s_bid_id);
        $this->set('s_package_id', $s_package_id);
        $this->set('s_promo_code', $s_promo_code);
        $this->set('s_format_id', $s_format_id);
        $this->set('s_site_id', $s_site_id);
        $this->set('s_tld_id', $s_tld_id);
        $this->set('s_offer_type_id', $s_offer_type_id);
        $this->set('s_ticket_status_id', $s_ticket_status_id);
        $this->set('s_res_confirmation_num', $s_res_confirmation_num);
        $this->set('s_res_check_in_date', $s_res_check_in_date);
        $this->set('s_has_promo', $s_has_promo);
        $this->set('s_manual_ticket', $s_manual_ticket);
        $this->set('s_start_y', $s_start_y);
        $this->set('s_start_m', $s_start_m);
        $this->set('s_start_d', $s_start_d);
        $this->set('s_end_y', $s_end_y);
        $this->set('s_end_m', $s_end_m);
        $this->set('s_end_d', $s_end_d);

        $tickets_index = $this->paginate();

        // redirect for ticket 1082
        if ($s_ticket_id) {
            if (sizeof($tickets_index) == 1) {
                header("location: /tickets/view/" . $tickets_index[0]['Ticket']['ticketId']);
                exit;
            }
        }

        foreach ($tickets_index as $k => $v) {
            $tickets_index[$k]['Ticket']['validCard'] = $this->getValidCcOnFile(
                $v['Ticket']['userId'],
                $v['Ticket']['bidId']
            );
            $clients = $this->Ticket->getClientsFromPackageId($v['Ticket']['packageId']);
            $tickets_index[$k]['Promo'] = $this->Ticket->getTicketPromoData($v['Ticket']['ticketId']);
            $tickets_index[$k]['Client'] = $clients;
            $tickets_index[$k]['ResPreferDate'] = array();
            if (in_array(
                    $v['Ticket']['offerTypeId'],
                    array(1, 2, 6)
                ) && !empty($v[0]['arrivalDate']) && !empty($v[0]['departureDate'])
            ) {
                $tickets_index[$k]['ResPreferDate']['arrival'] = $v[0]['arrivalDate'];
                $tickets_index[$k]['ResPreferDate']['departure'] = $v[0]['departureDate'];
                $tickets_index[$k]['ResPreferDate']['flagged'] = (strtotime(
                        $tickets_index[$k]['ResPreferDate']['arrival']
                    ) - strtotime('NOW') <= 604800) ? 1 : 0;
            } elseif ($v['Ticket']['formatId'] == 2) {
                $tickets_index[$k]['ResPreferDate']['arrival'] = $v['Ticket']['requestArrival'];
                $tickets_index[$k]['ResPreferDate']['departure'] = $v['Ticket']['requestDeparture'];
                $tickets_index[$k]['ResPreferDate']['flagged'] = (strtotime(
                        $tickets_index[$k]['ResPreferDate']['arrival']
                    ) - strtotime('NOW') <= 604800) ? 1 : 0;
            }
        }

        $csv_link_string = '/tickets/index/csv_export:1/';
        foreach ($this->params['form'] as $kk => $vv) {
            $csv_link_string .= "$kk:$vv/";
        }
        $csv_link_string .= '.csv';

        $this->set('csv_link_string', $csv_link_string);
        $this->set('tickets', $tickets_index);
        $this->set('format', $this->Format->find('list'));
        $this->set('offerType', $this->OfferType->find('list'));
        $ticketStatusIds = $this->Ticket->TicketStatus->find('list');
        unset($ticketStatusIds[6]);
        $this->set('ticketStatus', $ticketStatusIds);
    }

    function getValidCcOnFile($userId, $bidId = null)
    {
        $ups = $this->Readonly->query(
            "select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc"
        );
        if ($bidId && is_numeric($bidId)) {
            $ups_bid = $this->Readonly->query(
                "select * from userPaymentSetting as UserPaymentSetting where userPaymentSettingId = (select userPaymentSettingId from bid where bidId = $bidId)"
            );
            if (!empty($ups_bid)) {
                $ups = $ups_bid;
            }
        }
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

    function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->Ticket->recursive = 2;
        $ticket = $this->Ticket->read(null, $id);

        if ($ticket === false) {
            $this->Session->setFlash(__("Not Finding ticketId $id", true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $lltgServiceBuilder = $this->LltgServiceHelper->getServiceBuilderFromTldId($ticket['Ticket']['tldId']);
        $this->set('lltgServiceBuilder', $lltgServiceBuilder);

        $this->PaymentType->recursive = 0;
        foreach ($ticket['PaymentDetail'] as $k => $v) {
            $paymentType = $this->PaymentType->read(null, $v['paymentTypeId']);
            $ticket['PaymentDetail'][$k]['paymentTypeName'] = $paymentType['PaymentType']['paymentTypeName'];
        }

        $ticket['Client'] = $this->Ticket->getClientsFromPackageId($ticket['Ticket']['packageId']);
        $ticket['Promo'] = $this->Ticket->getTicketPromoData($id);

        $this->set('ticket', $ticket);
        $this->set('processingFee', $this->Ticket->getFeeByTicket($id));
        $this->set('currencyName', $this->Ticket->getCurrencyNameByTicketId($id));

        $this->set('offerType', $this->OfferType->find('list'));
        $this->set('ppvNoticeTypes', $this->Ticket->PpvNotice->PpvNoticeType->find('list'));

        $this->data = array();
        $this->data['condition1']['field'] = "Offer.offerId";
        $this->data['condition1']['value'] = $ticket['Ticket']['offerId'];
        $offer_search_serialize = serialize($this->data);
        $this->set('offer_search_serialize', $offer_search_serialize);

        // revenue stuff
        $tracks = $this->TrackDetail->getTrackRecord($id);
        $track = $tracks[0];
        $trackDetailExists = false;
        if ($track) {
            $trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $id);
            //$this->set('trackDetails', $this->TrackDetail->getAllTrackDetails($track['trackId']));
            $this->set('trackDetails', $this->TrackDetail->getAllTrackDetailsForTicket($id));
        }
        $this->set('trackExistsCount', $trackDetailExists ? 1 : 0);
        $this->set('trackDetailExists', $trackDetailExists);
        $this->set('track', $track);

        $showVoidLink = false;
        $showRefundLink = false;
        $currentUser = $this->LdapAuth->user();
        if (in_array('Accounting', $currentUser['LdapUser']['groups']) || in_array(
                'Geeks',
                $currentUser['LdapUser']['groups']
            )
        ) {
            $showVoidLink = true;
            $showRefundLink = true;
        }
        $this->set('showVoidLink', $showVoidLink);
        $this->set('showRefundLink', $showRefundLink);
        $this->set('showEditLink', $this->hasEditorAccess());

        $preferDatesUser = $this->ReservationPreferDate->find(
            'all',
            array(
                'conditions' => array('ticketId' => $id)
            ,
                'order' => array('reservationPreferDateTypeId ASC')
            )
        );
        $this->set('preferDatesUser', $preferDatesUser);

        $preferDatesHotel = $this->ReservationPreferDateFromHotel->find(
            'all',
            array(
                'conditions' => array('ticketId' => $id)
            ,
                'order' => array('reservationPreferDateFromHotelId ASC')
            )
        );
        $this->set('preferDatesHotel', $preferDatesHotel);
    }

    function phpinfoshow()
    {
        phpinfo();
        die();
    }

    function edit($id = null)
    {
        // only for updating ticket notes for now.  should not be able to update anything else.
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data) && !empty($this->data['Ticket']['ticketId'])) {
            if ($this->Ticket->save($this->data)) {
                $this->Session->setFlash(__('The ticket note has been saved.', true), 'default', array(), 'success');
                $this->redirect(array('action' => 'view', 'id' => $id));
            } else {
                $this->Session->setFlash(
                    __('The ticket note has not been saved due to an error.', true),
                    'default',
                    array(),
                    'error'
                );
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Ticket->read(null, $id);
        }
        if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
            $initials_user = $_SESSION['Auth']['AdminUser']['mailnickname'];
        } else {
            $initials_user = false;
        }

        // only ticket status edit only for CHRISTINE YOUNG
        //$allow_status_edit = in_array(trim($initials_user), array('cyoung','alee','bly')) ? true : false;
        // override june 23, 2010 -- allow everyone to edit status
        $allow_status_edit = true;

        $this->set('allow_status_edit', $allow_status_edit);
        $ticketStatusIds = $this->Ticket->TicketStatus->find('list');
        unset($ticketStatusIds[6]);
        $this->set('ticketStatusIds', $ticketStatusIds);
    }

    function updateDetails($id = null)
    {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!$this->hasEditorAccess()) {
            $this->Session->setFlash(
                __('You do not have permission to edit ticket details.', true),
                'default',
                array(),
                'error'
            );
            $this->redirect(array('action' => 'view', 'id' => $id));
        }

        if (!empty($this->data) && !empty($this->data['Ticket']['ticketId'])) {

            $arrivalDate = $this->dateArrayToString($this->data['Ticket']['requestArrival']);
            $departureDate = $this->dateArrayToString($this->data['Ticket']['requestDeparture']);
            $calculatedNights = round((strtotime($departureDate) - strtotime($arrivalDate)) / 86400);
            $error = false;

            if ($this->data['extraNotes'] == '') {
                $error = true;
                $this->Session->setFlash(__('Please add a note.', true), 'default', array(), 'error');
            }
            if ($calculatedNights != $this->data['Ticket']['numNights']) {
                $error = true;
                $this->Session->setFlash(
                    __('Arrival to Departure is not ' . $this->data['Ticket']['numNights'] . ' nights.', true),
                    'default',
                    array(),
                    'error'
                );
            }
            if (!$departureDate) {
                $error = true;
                $this->Session->setFlash(
                    __('Request Departure must be a valid date', true),
                    'default',
                    array(),
                    'error'
                );
            }
            if (!$arrivalDate) {
                $error = true;
                $this->Session->setFlash(__('Request Arrival must be a valid date', true), 'default', array(), 'error');
            }

            if (!$error) {
                if ($this->Ticket->save($this->data)) {
                    $currentUser = $this->LdapAuth->user();
                    $note = date("n/j/Y") . ' -- ' . $currentUser['LdapUser']['samaccountname'] . " modified ticket\n";
                    $note .= $this->data['extraNotes'] . "\n\n";
                    $q = "UPDATE ticket SET ticketNotes = CONCAT(?, IFNULL(ticketNotes, '')) WHERE ticketId = ?";
                    $this->Ticket->query($q, array($note, $this->data['Ticket']['ticketId']));

                    // requested reservation dates
                    $q = "DELETE FROM reservationPreferDate WHERE ticketId = ?";
                    $this->Ticket->query($q, array($this->data['Ticket']['ticketId']));

                    $q = "INSERT INTO reservationPreferDate (reservationPreferDateTypeId, ticketId, arrivalDate, departureDate, created) VALUES (1, ?, ?, ?, NOW())";
                    $this->Ticket->query($q, array($this->data['Ticket']['ticketId'], $arrivalDate, $departureDate));

                    $this->Session->setFlash(__('The ticket has been updated.', true), 'default', array(), 'success');
                    $this->redirect(array('action' => 'view', 'id' => $this->data['Ticket']['ticketId']));
                } else {
                    $this->Session->setFlash(
                        __('The ticket has not been saved due to an error.', true),
                        'default',
                        array(),
                        'error'
                    );
                }
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Ticket->read(null, $id);
            $this->data['extraNotes'] = '';
        }
    }

    private function dateArrayToString($dt)
    {
        $rtn = $dt['year'] . '-' . $dt['month'] . '-' . $dt['day'];
        if (Validation::date($rtn, 'ymd')) {
            return $rtn;
        } else {
            return false;
        }
    }

    function add()
    {
        if (!empty($this->data)) {
            if (!$this->data['Ticket']['offerId'] || !is_numeric($this->data['Ticket']['offerId'])) {
                $this->Session->setFlash(
                    __('The ticket was not created.  The offerId cannot be blank and must be a number.', true),
                    'default',
                    array(),
                    'error'
                );
            } elseif (!$this->data['Ticket']['userId'] || !is_numeric($this->data['Ticket']['userId'])) {
                $this->Session->setFlash(
                    __('The ticket was not created.  The userId cannot be blank and must be a number.', true),
                    'default',
                    array(),
                    'error'
                );
            } elseif (!$this->data['Ticket']['billingPrice'] || !is_numeric($this->data['Ticket']['billingPrice'])) {
                $this->Session->setFlash(
                    __('The ticket was not created.  The billingPrice cannot be blank and must be a number.', true),
                    'default',
                    array(),
                    'error'
                );
            } elseif (!$this->data['Ticket']['siteId'] || !is_numeric($this->data['Ticket']['siteId'])) {
                $this->Session->setFlash(
                    __('The ticket was not created.  You must select a site!', true),
                    'default',
                    array(),
                    'error'
                );
            } elseif (intval($this->data['Ticket']['billingPrice']) > 30000) {
                $this->Session->setFlash(
                    __('The ticket was not created.  Maximum Billing Price is 30,000', true),
                    'default',
                    array(),
                    'error'
                );
            } else {
                $this->User->recursive = 1;
                $userData = $this->User->read(null, $this->data['Ticket']['userId']);

                switch ($this->data['Ticket']['siteId']) {
                    case 1:
                        $this->OfferLuxuryLink->recursive = -1;
                        $tmp = $this->OfferLuxuryLink->read(null, $this->data['Ticket']['offerId']);
                        $offerData = $tmp['OfferLuxuryLink'];
                        break;
                    case 2:
                        $this->OfferFamily->recursive = -1;
                        $tmp = $this->OfferFamily->read(null, $this->data['Ticket']['offerId']);
                        $offerData = $tmp['OfferFamily'];
                        break;
                    default:
                        die('INVALID SITE ID - Please contact your friendly local developer.');
                        break;
                }

                if (empty($userData)) {
                    $this->Session->setFlash(
                        __('The ticket was not created.  Invalid User Id.', true),
                        'default',
                        array(),
                        'error'
                    );
                } elseif (empty($offerData)) {
                    $this->Session->setFlash(
                        __('The ticket was not created.  Invalid Offer Id.', true),
                        'default',
                        array(),
                        'error'
                    );
                } else {
                    $manual_datetime = date('Y-m-d H:i:s');
                    $this->data['Ticket']['ticketStatusId'] = 1;
                    $this->data['Ticket']['packageId'] = $offerData['packageId'];
                    $this->data['Ticket']['formatId'] = in_array($offerData['offerTypeId'], array(1, 2, 6)) ? 1 : 2;
                    $this->data['Ticket']['offerTypeId'] = $offerData['offerTypeId'];
                    $this->data['Ticket']['userFirstName'] = $userData['User']['firstName'];
                    $this->data['Ticket']['userLastName'] = $userData['User']['lastName'];
                    $this->data['Ticket']['userEmail1'] = $userData['User']['email'];
                    $this->data['Ticket']['userWorkPhone'] = $userData['User']['workPhone'];
                    $this->data['Ticket']['userHomePhone'] = $userData['User']['homePhone'];
                    $this->data['Ticket']['userMobilePhone'] = $userData['User']['mobilePhone'];
                    $this->data['Ticket']['userFax'] = $userData['User']['fax'];
                    $this->data['Ticket']['userAddress1'] = $userData['Address'][0]['address1'];
                    $this->data['Ticket']['userAddress2'] = $userData['Address'][0]['address2'];
                    $this->data['Ticket']['userCity'] = $userData['Address'][0]['address3'];
                    $this->data['Ticket']['userState'] = $userData['Address'][0]['stateName'];
                    $this->data['Ticket']['userCountry'] = $userData['Address'][0]['countryText'];
                    $this->data['Ticket']['userZip'] = $userData['Address'][0]['postalCode'];
                    $this->data['Ticket']['transmitted'] = 1;
                    $this->data['Ticket']['transmittedDatetime'] = $manual_datetime;
                    $this->data['Ticket']['inProcess'] = 0;
                    $this->data['Ticket']['inProcessDatetime'] = $manual_datetime;
                    $this->data['Ticket']['ticketNotes'] = 'MANUALLY CREATED TICKET' . trim(
                            $this->data['Ticket']['ticketNotes']
                        );
                    $this->data['Ticket']['numNights'] = intval($this->data['Ticket']['numNights']);

                    $this->Ticket->create();
                    if ($this->Ticket->save($this->data)) {
                        $this->Session->setFlash(__('This manual ticket has been created successfully', true));
                        $this->redirect(array('action' => 'view', 'id' => $this->Ticket->getLastInsertId()));
                    } else {
                        $this->Session->setFlash(__('The Ticket could not be saved. Please, try again.', true));
                    }
                }
            }
        }
        $this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
        $this->set('formatIds', $this->Format->find('list'));
        $this->set('offerTypeIds', $this->OfferType->find('list'));
        if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
            $manualTicketInitials = $_SESSION['Auth']['AdminUser']['mailnickname'];
        } else {
            $manualTicketInitials = 'MANUAL_TICKET';
        }
        $this->data['Ticket']['manualTicketInitials'] = $manualTicketInitials;
    }


    function add2012()
    {

        if (!$this->hasAddAccess()) {
            $this->Session->setFlash(
                __('You do not have permission to add manual tickets.', true),
                'default',
                array(),
                'error'
            );
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->data)) {

            $tData = $this->data['Ticket'];

            // validation
            $errors = array();
            if ($tData['siteId'] == '') {
                $errors[] = 'Please complete the Site Id field.';
            }
            if (intval($tData['packageId']) == 0) {
                $errors[] = 'Please complete the Package Id field.';
            }
            if (intval($tData['offerId']) == 0) {
                $errors[] = 'Please complete the Offer Id field.';
            }
            if (intval($tData['userId']) == 0) {
                $errors[] = 'Please complete the User Id field.';
            }
            if (intval($tData['userPaymentSettingId']) == 0) {
                $errors[] = 'Please complete the Credit Card field.';
            }
            if (intval($tData['billingPrice']) == 0) {
                $errors[] = 'Please complete the Billing Price field.';
            }
            if (intval($tData['numNights']) == 0) {
                $errors[] = 'Please complete the Num Nights field.';
            }

            $tldId = $tData['tldId'];

            $arrivalDate = $this->dateArrayToString($tData['requestArrival']);
            if (!$arrivalDate) {
                $errors[] = 'Request Arrival must be a valid date';
            }

            $insertPromoCodeId = false;
            if ($tData['promoCode'] != '') {
                $code = $this->PromoCode->findBypromoCode($tData['promoCode']);
                if (!$code) {
                    $errors[] = 'Promo code ' . $tData['promoCode'] . ' not found';
                } else {
                    //ticket #4659 - verify promotion can be used
                    $passcodeResult = $this->applyPasscode($tData['promoCode'], $tData['userId'], $tData['billingPrice'], $tData['offerId'], $tData['siteId'], $code['Promo'][0]['tldId']);
                    if ($tData['autoConfirm'] == 'N') {
                        $errors[] = 'Promo Codes require Auto Confirm - codes can be applied from the payment screen.';
                    } elseif (!$passcodeResult['success']) {
                        $errors[] = $passcodeResult['errorMessage'];;
                    }
                    elseif ($tldId != intval($code['Promo'][0]['tldId'])) {
                        $errors[] = $code['PromoCode']['promoCode'] . ' is not valid for locale #' . $tldId;

                    } else {
                        $insertPromoCodeId = $code['PromoCode']['promoCodeId'];
                    }
                }
            }

            if ($tData['autoConfirm'] == '') {
                $errors[] = 'Please complete the Auto Confirm field.';
            }
            if ($tData['autoConfirm'] == 'Y') {
                if ($tData['billingPrice'] != $tData['offerPrice']) {
                    $errors[] = 'Billing Price can not be modified for Auto Confirm tickets.';
                }
                if ($tData['numNights'] != $tData['offerNights']) {
                    $errors[] = 'Num Nights can not be modified for Auto Confirm tickets.';
                }
            }

            if (sizeof($errors) > 0) {
                $this->Session->setFlash(__((implode('<br />', $errors)), true));
            } else {

                // populate ticket
                $saveTicketData = array();
                $saveTicketData['manualTicketInitials'] = $tData['manualTicketInitials'];
                $saveTicketData['siteId'] = $tData['siteId'];
                $saveTicketData['billingPrice'] = $tData['billingPrice'];
                $saveTicketData['numNights'] = $tData['numNights'];
                $saveTicketData['requestNumGuests'] = $tData['requestNumGuests'];
                $saveTicketData['requestNotes'] = $tData['requestNotes'];
                $saveTicketData['userPaymentSettingId'] = $tData['userPaymentSettingId'];
                $saveTicketData['created'] = date('Y-m-d H:i:s');
                $saveTicketData['modified'] = date('Y-m-d H:i:s');
                $saveTicketData['ticketStatusId'] = 1;

                $saveTicketData['requestArrival'] = $tData['requestArrival'];
                $dTime = strtotime($arrivalDate) + ($tData['numNights'] * 86400);
                $saveTicketData['requestDeparture'] = array(
                    'month' => date('m', $dTime),
                    'day' => date('d', $dTime),
                    'year' => date('Y', $dTime)
                );

                $notes = ($tData['ticketNotes'] != '') ? "\n\n" . trim($tData['ticketNotes']) : '';
                $saveTicketData['ticketNotes'] = 'MANUALLY CREATED TICKET 2' . $notes;

                // offer info
                if ($tData['siteId'] == 1) {
                    $this->OfferLuxuryLink->recursive = -1;
                    $tmp = $this->OfferLuxuryLink->read(null, $tData['offerId']);
                    $offerData = $tmp['OfferLuxuryLink'];
                } elseif ($tData['siteId'] == 2) {
                    $this->OfferFamily->recursive = -1;
                    $tmp = $this->OfferFamily->read(null, $tData['offerId']);
                    $offerData = $tmp['OfferFamily'];
                }
                $saveTicketData['offerId'] = $tData['offerId'];
                $saveTicketData['bidId'] = null;
                $saveTicketData['packageId'] = $offerData['packageId'];
                $saveTicketData['formatId'] = in_array($offerData['offerTypeId'], array(1, 2, 6)) ? 1 : 2;
                $saveTicketData['offerTypeId'] = $offerData['offerTypeId'];
                $saveTicketData['tldId'] = $tldId;

                // user info
                $this->User->recursive = 1;
                $userData = $this->User->read(null, $tData['userId']);
                $saveTicketData['userId'] = $tData['userId'];
                $saveTicketData['userFirstName'] = $userData['User']['firstName'];
                $saveTicketData['userLastName'] = $userData['User']['lastName'];
                $saveTicketData['userEmail1'] = $userData['User']['email'];
                $saveTicketData['userWorkPhone'] = $userData['User']['workPhone'];
                $saveTicketData['userHomePhone'] = $userData['User']['homePhone'];
                $saveTicketData['userMobilePhone'] = $userData['User']['mobilePhone'];
                $saveTicketData['userFax'] = $userData['User']['fax'];
                $saveTicketData['userAddress1'] = $userData['Address'][0]['address1'];
                $saveTicketData['userAddress2'] = $userData['Address'][0]['address2'];
                $saveTicketData['userCity'] = $userData['Address'][0]['address3'];
                $saveTicketData['userState'] = $userData['Address'][0]['stateName'];
                $saveTicketData['userCountry'] = $userData['Address'][0]['countryText'];
                $saveTicketData['userZip'] = $userData['Address'][0]['postalCode'];

                if ($tData['autoConfirm'] == 'Y') {
                    $saveTicketData['transmitted'] = 0;
                    $saveTicketData['inProcess'] = 0;
                } else {
                    $saveTicketData['transmitted'] = 1;
                    $saveTicketData['transmittedDatetime'] = date('Y-m-d H:i:s');
                    $saveTicketData['inProcess'] = 0;
                    $saveTicketData['inProcessDatetime'] = date('Y-m-d H:i:s');
                }

                $this->Ticket->create();

                if ($this->Ticket->save($saveTicketData)) {
                    if ($insertPromoCodeId) {
                     //   $q = 'DELETE FROM promoOfferTracking WHERE user = ? AND offerId = ?';
                     //   $this->Ticket->query($q, array($tData['userId'], $tData['offerId']));
                        $q = 'INSERT promoOfferTracking (promoCodeId, userId, offerId, datetime) VALUES (?, ?, ?, NOW())';
                        $this->Ticket->query($q, array($insertPromoCodeId, $tData['userId'], $tData['offerId']));
                    }

                    $this->Session->setFlash(__('This manual ticket has been created successfully', true));
                    $this->redirect(array('action' => 'view', 'id' => $this->Ticket->getLastInsertId()));
                } else {
                    $this->Session->setFlash(__('The Ticket could not be saved. Please, try again.', true));
                }
            }
        } else {
            $currentUser = $this->LdapAuth->user();
            $this->data['Ticket']['manualTicketInitials'] = $currentUser['LdapUser']['samaccountname'];
            $this->data['Ticket']['departureDisplay'] = '-';
        }

        $tldParam = (isset($tldId)) ? $tldId : 1;

        $packageList = (isset($this->data['Ticket']['siteId']) && isset($this->data['Ticket']['clientId'])) ? $this->mtPackagesBySiteAndClient(
            $this->data['Ticket']['siteId'],
            $this->data['Ticket']['clientId'],
            $tldParam
        ) : array();
        $offerList = (isset($this->data['Ticket']['siteId']) && isset($this->data['Ticket']['packageId'])) ? $this->mtOffersBySiteAndPackage(
            $this->data['Ticket']['siteId'],
            $this->data['Ticket']['packageId'],
            $tldParam
        ) : array();
        $ccList = (isset($this->data['Ticket']['userId'])) ? $this->mtCcsByUser(
            $this->data['Ticket']['userId']
        ) : array();

        $this->set('packageList', $packageList);
        $this->set('offerList', $offerList);
        $this->set('ccList', $ccList);
    }


    function search()
    {
        if (!empty($_GET['query'])) {
            $this->params['form']['query'] = $_GET['query'];
        } elseif (!empty($this->params['named']['query'])) {
            $this->params['form']['query'] = $this->params['named']['query'];
        }
        if (!empty($this->params['form']['query'])):
            $query = $this->Sanitize->escape($this->params['form']['query']);

            $queryPieces = explode(" ", $query);

            $sqlquery = '';
            foreach ($queryPieces as $piece) {
                if (strlen($piece) > 3) {
                    $sqlquery .= '+';
                }
                $sqlquery .= $piece . '* ';
            }

            $this->Client->recursive = -1;
            $conditions = array("(MATCH(Client.name) AGAINST('$sqlquery' IN BOOLEAN MODE))");

            $results = $this->Client->find(
                'all',
                array(
                    'conditions' => $conditions,
                    'limit' => 5
                )
            );

            $this->set('query', $query);
            $this->set('results', $results);

            if (isset($this->params['requested'])) {
                return $results;
            } elseif (@$_GET['query'] || @ $this->params['named']['query']) {
                $this->redirect(array('controller' => 'tickets', 'action' => 'index/?query=' . $query));
            }
        endif;
    }

    function revenue()
    {

        // set search criteria from form post or set defaults
        $form = $this->params['form'];
        $named = $this->params['named'];

        // ajaxed paginated form elements come in via params['named']
        if (empty($form) && !empty($named)) {
            $form = $named;
            $this->params['form'] = $this->params['named'];
        }

        // set values and set defaults
        $s_ticket_id = isset($form['s_ticket_id']) ? $form['s_ticket_id'] : '';
        $s_offer_id = isset($form['s_offer_id']) ? $form['s_offer_id'] : '';
        $s_format_id = isset($form['s_format_id']) ? $form['s_format_id'] : '';
        $s_client_id = isset($form['s_client_id']) ? $form['s_client_id'] : '';
        $s_start_y = isset($form['s_start_y']) ? $form['s_start_y'] : date('Y');
        $s_start_m = isset($form['s_start_m']) ? $form['s_start_m'] : date('m');
        $s_start_d = isset($form['s_start_d']) ? $form['s_start_d'] : date('d');
        $s_end_y = isset($form['s_end_y']) ? $form['s_end_y'] : date('Y');
        $s_end_m = isset($form['s_end_m']) ? $form['s_end_m'] : date('m');
        $s_end_d = isset($form['s_end_d']) ? $form['s_end_d'] : date('d');

        if (isset($_GET['searchClientId'])) {
            $s_client_id = $_GET['searchClientId'];
        }

        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            if (is_numeric($query)) {
                $s_ticket_id = $_GET['query'];
            }
        }

        $this->set('s_ticket_id', $s_ticket_id);
        $this->set('s_offer_id', $s_offer_id);
        $this->set('s_client_id', $s_client_id);
        $this->set('s_format_id', $s_format_id);
        $this->set('s_start_y', $s_start_y);
        $this->set('s_start_m', $s_start_m);
        $this->set('s_start_d', $s_start_d);
        $this->set('s_end_y', $s_end_y);
        $this->set('s_end_m', $s_end_m);
        $this->set('s_end_d', $s_end_d);

        // use these dates in the sql for date range search
        $s_start_date = $s_start_y . '-' . $s_start_m . '-' . $s_start_d . ' 00:00:00';
        $s_end_date = $s_end_y . '-' . $s_end_m . '-' . $s_end_d . ' 23:59:59';

        $this->paginate = array(
            'fields' => array(
                'Ticket.ticketId',
                'Ticket.created',
                'Ticket.offerId',
                'Ticket.packageId',
                'Ticket.billingPrice',
                'Ticket.formatId'
            ),
            'contain' => array('TicketStatus'),
            'order' => array(
                'Ticket.ticketId' => 'desc'
            )
        );

        // if search via ticket id, offer id, or user id, then dont use other search conditions
        if ($s_ticket_id) {
            $this->paginate['conditions']['Ticket.ticketId'] = $s_ticket_id;
        } elseif ($s_offer_id) {
            $this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;
        } elseif ($s_client_id) {
            $this->paginate['conditions']['Ticket.clientId'] = $s_client_id;
        } else {
            $this->paginate['conditions']['Ticket.created BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);
            if ($s_format_id) {
                $this->paginate['conditions']['Ticket.formatId'] = $s_format_id;
            }
        }

        $tickets_index = $this->paginate();

        // TODO : OPTIMIZE THIS SECTION
        foreach ($tickets_index as $k => $v) {
            $paymentDetail = $this->Ticket->query(
                "select sum(ppBillingAmount) as sumPayment from paymentDetail where isSuccessfulCharge = 1 and ticketId = " . $v['Ticket']['ticketId']
            );
            $sumPayment = !empty($paymentDetail) && isset($paymentDetail[0][0]['sumPayment']) ? $paymentDetail[0][0]['sumPayment'] : 0;
            $tickets_index[$k]['Ticket']['sumPayment'] = '$' . number_format($sumPayment, 2, '.', ',');
            $clients = $this->Ticket->getClientsFromPackageId($v['Ticket']['packageId']);
            $tickets_index[$k]['Client'] = $clients;
            $tracks = $this->TrackDetail->getTrackRecord($v['Ticket']['ticketId']);
            if (!empty($tracks)) {
                foreach ($tracks as $a => $track) {
                    $tracks[$a]['trackDetail'] = $this->TrackDetail->getExistingTrackTicket(
                        $track['trackId'],
                        $v['Ticket']['ticketId']
                    );
                    if (!empty($tracks[$a]['trackDetail'])) {
                        $tracks[$a]['trackDetail']['status'] = '1';
                        $tracks[$a]['trackDetail']['allocatedAmount'] = '$' . number_format(
                                $tracks[$a]['trackDetail']['allocatedAmount'],
                                2,
                                '.',
                                ','
                            );
                        $tracks[$a]['trackDetail']['amountKept'] = '$' . number_format(
                                $tracks[$a]['trackDetail']['amountKept'],
                                2,
                                '.',
                                ','
                            );
                        $tracks[$a]['trackDetail']['amountRemitted'] = '$' . number_format(
                                $tracks[$a]['trackDetail']['amountRemitted'],
                                2,
                                '.',
                                ','
                            );
                    } else {
                        $tracks[$a]['trackDetail']['allocatedAmount'] = '-';
                        $tracks[$a]['trackDetail']['amountKept'] = '-';
                        $tracks[$a]['trackDetail']['amountRemitted'] = '-';
                        $tracks[$a]['trackDetail']['status'] = '0';
                    }
                }
                $tickets_index[$k]['Ticket']['is_multi_track'] = count($tracks) > 1 ? true : false;
            } else {
                $tracks[0]['trackId'] = '<strong>NO TRACK!</strong>';
                $tracks[0]['trackName'] = '<strong>NO TRACK!</strong>';
                $tracks[0]['trackDetail']['allocatedAmount'] = '<strong>NO TRACK!</strong>';
                $tracks[0]['trackDetail']['amountKept'] = '<strong>NO TRACK!</strong>';
                $tracks[0]['trackDetail']['amountRemitted'] = '<strong>NO TRACK!</strong>';
                $tracks[0]['trackDetail']['status'] = '0';
                $tickets_index[$k]['Ticket']['is_multi_track'] = false;
            }
            $tickets_index[$k]['Tracks'] = $tracks;
        }
        $this->set('tickets', $tickets_index);
        $this->set('format', $this->Format->find('list'));
    }

    private function hasEditorAccess()
    {
        $currentUser = $this->LdapAuth->user();
        $editGroups = array('Accounting', 'Geeks');
        foreach ($editGroups as $eg) {
            if (in_array($eg, $currentUser['LdapUser']['groups'])) {
                return true;
            }
        }
        return false;
    }

    private function hasAddAccess()
    {
        $currentUser = $this->LdapAuth->user();
        $editGroups = array('Accounting', 'Geeks', 'Concierge', 'conciergeDL');
        foreach ($editGroups as $eg) {
            if (in_array($eg, $currentUser['LdapUser']['groups'])) {
                return true;
            }
        }
        return false;
    }

    function mt_packagelist_ajax()
    {
        $clientId = $this->params['url']['clientId'];
        if (intval($clientId) > 0) {
            $packages = $this->mtPackagesBySiteAndClient(
                $this->params['url']['siteId'],
                $clientId,
                $this->params['url']['tldId']
            );
        } else {
            $packages = array();
        }
        if (sizeof($packages) == 0) {
            $packages[0] = 'No Live Packages';
        }
        echo json_encode(array('packages' => $packages));
        exit;
    }

    function mt_offerlist_ajax()
    {
        $offers = $this->mtOffersBySiteAndPackage(
            $this->params['url']['siteId'],
            $this->params['url']['packageId'],
            $this->params['url']['tldId']
        );
        echo json_encode(array('offers' => $offers));
        exit;

    }

    function mt_cclist_ajax()
    {
        $userId = $this->params['url']['userId'];
        if (intval($userId) > 0) {
            $ccs = $this->mtCcsByUser($userId);
        } else {
            $ccs = array();
        }
        if (sizeof($ccs) == 0) {
            $ccs[0] = 'No Valid Credit Cards';
        }
        echo json_encode(array('ccs' => $ccs));
        exit;

    }

    private function mtPackagesBySiteAndClient($siteId, $clientId, $tldId)
    {
        if ($tldId == 2) {
            $allowedTypes = array(3, 4);
        } else {
            $allowedTypes = array(1, 2, 3, 4, 6);
        }
        $offerTable = ($siteId == '2') ? 'offerFamily' : 'offerLuxuryLink';
        $q = 'SELECT DISTINCT p.packageId, p.packageName
				FROM ' . $offerTable . ' o
				INNER JOIN package p USING(packageId)
				WHERE o.clientId = ? 
				AND o.startDate < NOW() 
				AND o.isClosed = 0 
				AND o.endDate > NOW()
				AND o.offerTypeId IN (' . implode(',', $allowedTypes) . ')';
        $result = $this->Ticket->query($q, array($clientId));
        $packages = array();
        foreach ($result as $r) {
            $packages[$r['p']['packageId']] = $r['p']['packageId'] . ' - ' . $r['p']['packageName'];
        }
        return $packages;
    }

    private function mtOffersBySiteAndPackage($siteId, $packageId, $tldId)
    {

        if ($tldId == 2) {
            $allowedTypes = array(3, 4);
            // $ukServiceBuilder = $this->LltgServiceHelper->getServiceBuilderFromTldId($tldId);
            // $currencyService = $this->LltgServiceHelper->getCurrencyService($ukServiceBuilder);
        } else {
            $allowedTypes = array(1, 2, 3, 4, 6);
        }
        $offerTable = ($siteId == '2') ? 'offerFamily' : 'offerLuxuryLink';
        $q = 'SELECT offerId, offerTypeId, offerTypeName, openingBid, roomNights, buyNowPrice, numGuests, endDate
				FROM ' . $offerTable . ' o
				WHERE o.packageId = ?
				AND startDate < NOW()
				AND o.offerTypeId IN (' . implode(',', $allowedTypes) . ')
				AND (
				(isClosed = 0 AND endDate > NOW())
				OR
				(isClosed = 1 AND endDate > NOW() - INTERVAL 7 DAY)
				) ORDER BY offerTypeName, openingBid, buyNowPrice, offerId';
        $result = $this->Ticket->query($q, array($packageId));
        $offers = array();
        foreach ($result as $r) {
            $price = ($r['o']['offerTypeId'] == 3 || $r['o']['offerTypeId'] == 4) ? $r['o']['buyNowPrice'] : $r['o']['openingBid'];
            $type = $r['o']['offerTypeName'];
            $nights = $r['o']['roomNights'] . ' nights';
            $guests = $r['o']['numGuests'] . ' guests';
            $dates = '';
            $ends = strtotime($r['o']['endDate']);
            if ($ends < time()) {
                $dates .= ' closed ' . date('m/d', $ends);
            } else {
                $dates .= ' live';
            }

            $tldPrice = '';
            if ($tldId == 2) {
                // $tldPrice = $currencyService->getLocalCurrencyFromDollars($price) . ' GBP';
            }

            $offers[$r['o']['offerId']] = $r['o']['offerId'] . ' - ' . $type . ' : ' . $nights . ' : ' . $guests . ' : $' . $price . ' : ' . $tldPrice . ' : ' . $dates;
        }
        return $offers;
    }

    /**
     * @Todo, WE are assuming that we are ONLY using this tool for clients who have user payment settings. Validate that is actually the case.
     */
    private function mtCcsByUser($userId)
    {
        $q = 'SELECT * FROM userPaymentSetting p WHERE inactive = 0 AND userId = ?';
        $result = $this->Ticket->query($q, array($userId));
        $ccs = array();
        foreach ($result as $r) {
            $dtExp = $r['p']['expYear'] . '-' . $r['p']['expMonth'] . '-2';
            $dtNow = date('Y-m') . '-1';
            if (strtotime($dtExp) > strtotime($dtNow)) {
                $ccNumber = $r['p']['ccToken'];
                $ccs[$r['p']['userPaymentSettingId']] = $r['p']['userPaymentSettingId'] . ' - ' . $r['p']['ccType'] . ' - XXXX-XXXX-XXXX-' . substr(
                        $ccNumber,
                        -4
                    );
            }
        }
        return $ccs;
    }
    /*
     * In case we need later.
     */
    public function isRegisteredUser($userId = null)
    {

        Configure::write('debug', '0');
        $this->autoRender = false;
        $this->autoLayout = false;

        $response = array();
        $isRegistered = 0;
        if (empty($userId)) {
            $isRegistered = 0;
        }
        $conditions = array(
            'UserSiteExtended.userId' => $userId,
        );
        $fields = array(
            'User.userId',
            'User.firstName',
            'User.lastName',
            'User.email',
            'UserSiteExtended.username',
            'UserSiteExtended.registrationDatetime',
            'UserSiteExtended.lastLogin',
        );
        $userDetails = $this->User->find(
            'all',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );
        if (!empty($userDetails)) {
            $isRegistered = 1;
        }
        $response = array('registered' => $isRegistered, 'userData' => $userDetails);
        header('Content-Type: application/javascript');
        echo $_GET['callback'] . '(' . json_encode($response) . ')';
        $this->set($response);
    }
    public function applyPasscode($passcode, $userId, $userPrice, $siteId, $tldCheck)
    {
        $result = array('success' => false);

        $promo = new PromoCode();
        $promo->checkPromoCode($passcode, $userId, $userPrice, $siteId, $tldCheck);

        if ($promo->isValidPromoCode) {
            if (isset($this->previousPromoInfo)) {
                $result['errorMessage'] =  $this->Translation->getTranslationForKey('TEXT_LABEL_PROMO_CODE_ERROR_PREVIOUS');
            } else {
                $this->passcodePromo = $passcode;
                $result['success'] = self::PASSCODE_STATUS_PROMO;
            }
        } else {
            $promoError = $this->Translation->getTranslationForKey('TEXT_LABEL_PROMO_CODE_ERROR_INVALID');
            if (is_array($promo->errors) && isset($promo->errors[0]) && strpos(
                    $promo->errors[0],
                    'display|'
                ) !== false
            ) {
                $errorAdditional = explode('|', $promo->errors[0]);
                $promoError .= '<br>' . $errorAdditional[1];
            }
            $result['success'] = false;
            $result['errorMessage'] = $promoError;
          //$result['errorMessage'] = "test";
        }
        return $result;
    }
    public function setServiceBuilder($v)
    {
        $this->serviceBuilder = $v;
        return $this;
    }


}
