<?php

class PgBookingsController extends AppController
{
    var $name = 'PgBookings';
    var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');


    function index()
    {
        // Readonly db config
        /*
        $this->Ticket->useReadonlyDb();
        $this->Ticket->TicketStatus->useReadonlyDb();
        $this->Ticket->PromoTicketRel->useReadonlyDb();
        $this->Ticket->Reservation->useReadonlyDb();
        */

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
                'PgBooking.pgBookingStatusId',
                'Client.name',
                'PgBooking.travelerFirstName',
                'PgBooking.travelerLastName',
                'User.firstName',
                'User.lastName',
                'User.email',
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
        } else {
            if (!$single_search_override) {
                $s_tld_id = null;
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
            if (sizeof($tickets_index) == 1) {
                header("location: /pg_bookings/view/" . $bookings_index[0]['PgBooking']['bookingId']);
                exit;
            }
        }

        $csv_link_string = '/pg_bookings/index/csv_export:1/';
        foreach ($this->params['form'] as $kk => $vv) {
            $csv_link_string .= "$kk:$vv/";
        }
        $csv_link_string .= '.csv';

        $this->set('csv_link_string', $csv_link_string);
        $this->set('bookings', $bookings_index);
    }


    function view($id = null)
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

        $this->set('booking', $booking);
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





}
