<?php

class PgBookingsController extends AppController
{
    public $name = 'PgBookings';
    public $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');

    public $LltgServiceHelper;

    public $uses = array(
        'PgBooking',
        'OfferType',
        'Format',
        'User',
        'ClientLoaPackageRel',
        'Track',
        'TrackDetail',
        'Offer',
        'Notes',
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

    public $components = array(
        'LltgServiceHelper'
    );

    public function index()
    {
        // Readonly db config
        $this->PgBooking->useReadonlyDb();

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
        $s_booking_id = isset($form['s_booking_id']) ? $form['s_booking_id'] : '';
        $s_user_id = isset($form['s_user_id']) ? $form['s_user_id'] : '';
        $s_tld_id = isset($form['s_tld_id']) ? $form['s_tld_id'] : '';
        $s_booking_status_id = isset($form['s_booking_status_id']) ? $form['s_booking_status_id'] : '';
        $s_client_id = isset($form['s_client_id']) ? $form['s_client_id'] : '';
        $s_quick_link = isset($form['s_quick_link']) ? $form['s_quick_link'] : '';
        $s_package_id = isset($form['s_package_id']) ? $form['s_package_id'] : '';
        $s_promo_code = isset($form['s_promo_code']) ? $form['s_promo_code'] : '';
        $s_has_promo = isset($form['s_has_promo']) ? $form['s_has_promo'] : '';
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
                $s_booking_id = $_GET['query'];
            }
        }

        $allowed_query_keys = array(
            's_quick_link',
            's_tld_id'
        );
        foreach ($this->params['url'] as $key => $value) {
            if (in_array($key, $allowed_query_keys)) {
                $$key = $value;
                $this->params['form'][$key] = $value;
            }
        }

        // use these dates in the sql for date range search
        $s_start_date = $s_start_y . '-' . $s_start_m . '-' . $s_start_d . ' 00:00:00';
        $s_end_date = $s_end_y . '-' . $s_end_m . '-' . $s_end_d . ' 23:59:59';

        $this->paginate = array(
            'fields' => array(
                'PgBooking.pgBookingId',
                'PgBooking.tldId',
                'PgBooking.dateCreated',
                'PgBooking.clientId',
                'PgBooking.userId',
                'PgBooking.clientId',
                'PgBooking.dateIn',
                'PgBooking.dateOut',
                'PgBooking.pgBookingStatusId',
                'PgBooking.promoCodeId',
                'PgBooking.travelerFirstName',
                'PgBooking.travelerLastName',
                'PgBooking.grandTotalUSD',
            ),
            'order' => array(
                'PgBooking.pgBookingId' => 'desc'
            ),
            'limit' => 50
        );

        if ($csv_export) {
            $this->paginate['limit'] = 10000;
        }

        $single_search = true;
        $single_search_override = false;
        // if search via ticket id, offer id, or user id, then dont use other search conditions
        if ($s_booking_id) {
            $this->paginate['conditions']['PgBooking.pgBookingId'] = $s_booking_id;
        } elseif (isset($s_quick_link) && !empty($s_quick_link)) {
            switch ($s_quick_link) {
                case 1:
                    break;
            }
            $single_search_override = true;
        } elseif ($s_user_id) {
            $this->paginate['conditions']['PgBooking.userId'] = $s_user_id;
        } elseif ($s_client_id) {
            $this->paginate['conditions']['PgBooking.clientId'] = $s_client_id;
        } elseif ($s_package_id) {
            $this->paginate['conditions']['PgBooking.packageId'] = $s_package_id;
        } elseif ($s_promo_code) {
            $promoCodeResult = $this->PromoCode->findBypromoCode($s_promo_code);
            $s_promo_code_id = $promoCodeResult['PromoCode']['promoCodeId'];
            $this->paginate['conditions']['PgBooking.promoCodeId'] = $s_promo_code_id;
        } else {
            $single_search = false;
            $this->paginate['conditions']['PgBooking.dateCreated BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);
            if ($s_tld_id) {
                $this->paginate['conditions']['PgBooking.tldId'] = $s_tld_id;
            }
            if ($s_booking_status_id) {
                $this->paginate['conditions']['PgBooking.pgBookingStatusId'] = $s_booking_status_id;
            }

        }

        // allow package/client/user/pricePoint to use date and status
        if ($s_package_id || $s_client_id || $s_user_id) {
            $single_search = false;
            if ($s_tld_id) {
                $this->paginate['conditions']['PgBooking.tldId'] = $s_tld_id;
            }
        }

        if (!$single_search) {
            $s_ticket_id = null;
            $s_booking_status_id = null;
        } else {
            if (!$single_search_override) {
                $s_tld_id = null;
                $s_booking_status_id = null;
            }
            $s_start_y = $s_end_y = date('Y');
            $s_start_m = $s_end_m = date('m');
            $s_start_d = $s_end_d = date('d');
        }

        $this->set('s_booking_id', $s_booking_id);
        $this->set('s_user_id', $s_user_id);
        $this->set('s_client_id', $s_client_id);
        $this->set('s_package_id', $s_package_id);
        $this->set('s_promo_code', $s_promo_code);
        $this->set('s_tld_id', $s_tld_id);
        $this->set('s_start_y', $s_start_y);
        $this->set('s_start_m', $s_start_m);
        $this->set('s_start_d', $s_start_d);
        $this->set('s_end_y', $s_end_y);
        $this->set('s_end_m', $s_end_m);
        $this->set('s_end_d', $s_end_d);

        $bookings_index = $this->paginate();

        if ($s_booking_id) {
            if (sizeof($bookings_index) == 1) {
                header("location: /pg_bookings/view/" . $bookings_index[0]['PgBooking']['bookingId']);
                exit;
            }
        }

        foreach ($bookings_index as $k => $v) {
            $bookings_index[$k]['PgBooking']['validCard'] = $this->getValidCcOnFile(
                $v['PgBooking']['userId']
            );
            if ($v['PgBooking']['promoCodeId']) {
                $bookings_index[$k]['Promo'] = $this->PgBooking->getTicketPromoData($v['PgBooking']['promoCodeId']);
            }
        }

        $csv_link_string = '/pg_bookings/index/csv_export:1/';
        foreach ($this->params['form'] as $kk => $vv) {
            $csv_link_string .= "$kk:$vv/";
        }
        $csv_link_string .= '.csv';

        $this->set('csv_link_string', $csv_link_string);
        $this->set('bookings', $bookings_index);
        $this->set('bookingStatusDisplay', $this->PgBooking->getStatusDisplay());
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

    public function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Booking.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->PgBooking->recursive = 1;
        $booking = $this->PgBooking->read(null, $id);

        if ($booking === false) {
            $this->Session->setFlash(__("Not Finding bookingId $id", true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $lltgServiceBuilder = $this->LltgServiceHelper->getServiceBuilderFromTldId($booking['PgBooking']['tldId']);
        $this->set('lltgServiceBuilder', $lltgServiceBuilder);

        if ($booking['PgBooking']['promoCodeId']) {
           $booking['Promo'] = $this->PgBooking->getTicketPromoData($booking['PgBooking']['promoCodeId']);
        }
        $booking['Notes'] = $this->Notes->getNotesByPgBooking($booking['PgBooking']['pgBookingId']);
        $this->set('booking', $booking);
        $this->set('bookingStatusDisplay', $this->PgBooking->getStatusDisplay());
        $this->set('ppvNoticeTypes', $this->PgBooking->PpvNotice->PpvNoticeType->find('list'));
    }

    public function edit($id = null)
    {
        // only for updating ticket notes for now.  should not be able to update anything else.
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data) && !empty($this->data['Booking']['pgBicketId'])) {
            if ($this->pgBooking->save($this->data)) {
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
            $this->data = $this->pgBooking->read(null, $id);
        }
        if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
            $initials_user = $_SESSION['Auth']['AdminUser']['mailnickname'];
        } else {
            $initials_user = false;
        }

        $allow_status_edit = true;

        $this->set('allow_status_edit', $allow_status_edit);
        $ticketStatusIds = $this->Ticket->TicketStatus->find('list');
        unset($ticketStatusIds[6]);
        $this->set('ticketStatusIds', $ticketStatusIds);
    }

    public function cancel($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Booking.', true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->PgBooking->recursive = 2;
        $booking = $this->PgBooking->read(null, $id);

        if ($booking === false) {
            $this->Session->setFlash(__("Not Finding bookingId $id", true), 'default', array(), 'error');
            $this->redirect(array('action' => 'index'));
        }

        // run cancel
        if (isset($this->params['url']['confirm']) && $this->params['url']['confirm'] == $id) {

            // api call for pegasus cancellation
            $postData = array('bookingId' => $id);

            $ch = curl_init(Configure::read('LltgApiUrl') . '/api-internal/v1/cancel');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $pegasusResult = json_decode(curl_exec($ch), 1);
            curl_close($ch);

            // cancellation notice
            $ppvData = array();
            $ppvData['pgBookingId'] = $id;
            $ppvData['send'] = 1;
            $ppvData['ppvNoticeTypeId'] = 61;

            $currentUser = $this->LdapAuth->user();
            if (isset($currentUser['LdapUser']['samaccountname'])) {
                $ppvInitials = $currentUser['LdapUser']['samaccountname'];
            } else {
                $ppvInitials = 'TOOLBOX';
            }
            $ppvData['initials'] = $ppvInitials;
            $data_json_encoded = json_encode($ppvData);
            $webservice_live_url = Configure::read("Url.Ws") . "/web_service_tickets/?wsdl";
            $soap_client = new SoapClient($webservice_live_url, array("exception" => 1));
            $response = $soap_client->ppv($data_json_encoded);

            if (isset($pegasusResult['status']) && $pegasusResult['status'] == '200') {
                $this->Session->setFlash(__("Booking $id has been canceled", true), 'default', array(), 'error');
                $this->redirect(array('action' => 'view', 'id' => $id));
            }
        }

        $this->set('booking', $booking);
        $this->set('bookingStatusDisplay', $this->PgBooking->getStatusDisplay());
    }
}
