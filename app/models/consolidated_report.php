<?php
/**
 * Model for consolidated report queries
 * 
 * Queries:
 * 1. Booking information (Current Month and Year-to-Date)
 * 	a. Auctions live
 * 	b. Fixed Price Live
 * 	c. Booking Requests
 * 	d. Room Nights
 * 	e. Gross Bookings
 * 	f. Commission Rate
 * 	g. Net Revenue
 * 
 * 2. Impressions (By Site and by Type. Year-to-Date, by month)
 * 	a. By Site
 * 	b. By Type
 * 		i.		Portfolio Microsite
 * 		ii.		Destination / Home
 * 		iii.	Searches / Listings
 * 		iv.		Email Newsletters
 * 		v. 		Social Media
 * 		vi.		Advertising (on site)
 * 		vii.	Advertising (off-site) 
 * 
 * 3. Leads by Geo (Current month. includes emails, leads, calls, and bookings)
 * 
 * 4. Contact Details (Current month. Traveler data from all Emails, Leads, Calls, and Bookings)
 * 
 * 
 */
App::import('Model', 'Client');
App::import('Model', 'Loa');
App::import('Model', 'User');
class ConsolidatedReport extends AppModel
{
	public $useTable = false;
	
	/**
	 * Client Model
	 *
	 * @access	private
	 * @param	object
	 */
	private $Client;
	
	/**
	 * Loa Model
	 *
	 * @access	private
	 * @param	object
	 */
	private $Loa;
	
	/**
	 * User Model
	 *
	 * @access	private
	 * @param	object
	 */
	private $User;
	
	/**
	 * client id
	 * 
	 * @access	private
	 * @param	int
	 */
	private $client_id;
	
	/**
	 * report date
	 *
	 * @access	private
	 * @param	string
	 */
	private $report_date;
	
	/**
	 * loa start date
	 * 
	 * @access	private
	 * @param	string
	 */
	private $loa_start_date;
	
	/**
	 * loa end date
	 * 
	 * @access	private
	 * @param	string
	 */
	private $loa_end_date;
	
	/**
	 * month start date
	 *
	 * @access	private
	 * @param	string
	 */
	private $month_start_date;
	
	/**
	 * month end date
	 *
	 * @access	private
	 * @param	string
	 */
	private $month_end_date;
	
	/**
	 *
	 */
	public function __construct()
	{
		$this->Client = new Client();
		$this->Loa = new Loa();
		$this->User = new User();
		parent::__construct();
	}
	
	/**
	 * Initialize private variables required to build a report and do some
	 * basic validation / sanity checks
	 * 
	 * @access	public
	 * @param	int client id
	 * @param	string start date
	 * @param	string end date
	 * @return	boolean
	 */
	public function create($client_id, $report_date, $loa_start_date, $loa_end_date)
	{
		$this->client_id = $client_id;
		$this->report_date = $report_date;
		$this->loa_start_date = $loa_start_date;
		$this->loa_end_date = $loa_end_date;

		return $this->validates();
	}
	
	/**
	 * Determine if required variables are valid before generating report
	 * 
	 * @access	public
	 * @return	boolean
	 */
	public function validates()
	{
		$isValid = true;
		if (!isset($this->client_id)) {
			$isValid = false;
		} else if (!isset($this->report_date)) {
			$isValid = false;
		} else if (!isset($this->loa_start_date)) {
			$isValid = false;
		} else if (!isset($this->loa_end_date)) {
			$isValid = false;
		} else if ($this->loa_start_date >= $this->loa_end_date) {
			$isValid = false;
		}
		
		if ($isValid) {
			if (substr($this->report_date, 0, 7) === substr($this->loa_start_date, 0, 7)) {
				$this->month_start_date = $this->loa_start_date;
			} else {
				$this->month_start_date = substr($this->report_date, 0, 7) . '-01';
			}
			
			$this->month_end_date = substr($this->report_date, 0, 7) . '-' . cal_days_in_month(CAL_GREGORIAN, date('n', strtotime($this->report_date)), date('Y', strtotime($this->report_date)));
			if (substr($this->month_end_date, 0, 7) === substr($this->loa_end_date, 0, 7)) {
				$this->month_end_date = $this->loa_end_date;
			}
		}

		return $isValid;
	}
	
	/**
	 * Return the client id
	 * 
	 * @access	public
	 * @return	int
	 */
	public function getClientId()
	{
		return $this->client_id;
	}
	
	/**
	 *
	 */
	public function getClientDetails($client_id)
	{
		$this->Client->id = $client_id;
		$client_details = $this->Client->find('first', array('recursive' => -1));
		
		$account_manager = $this->User->find(
			'first',
			array(
				'recursive' => -1,
				'fields' => array('firstname', 'lastname', 'email'),
				'conditions' => array(
					'email' => $client_details['Client']['managerUsername'] . '@luxurylink.com'
				)
			)
		);

		$client_details['AccountManager']['name'] = $account_manager['User']['firstname'] . ' ' . $account_manager['User']['lastname'];
		$client_details['AccountManager']['email'] = $account_manager['User']['email'];
		
		return $client_details;
	}
	
	/**
	 *
	 */
	public function getLoaDetails($client_id, $report_date)
	{
		$loa_details = $this->Loa->find(
			'first',
			array(
				'recursive' => -1,
				'fields' => array('clientId', 'loaId', 'startDate', 'endDate', 'membershipFee'),
				'conditions' => array(
					"'$report_date' BETWEEN startDate AND endDate",
					"clientId = $client_id"
				)
			)
		);

		return $loa_details;
	}
	
	/**
	 * Return the report loa start date
	 * 
	 * @access	public
	 * @return	string
	 */
	public function getLoaStartDate()
	{
		return $this->loa_start_date . ' 00:00:00';
	}
	
	/**
	 * Return the report month start date
	 * 
	 * @access	public
	 * @return	string
	 */
	public function getMonthStartDate()
	{
		return $this->month_start_date . ' 23:59:59';
	}
	
	/**
	 * Return the report month end date
	 * 
	 * @access	public
	 * @return	string
	 */
	public function getMonthEndDate()
	{
		return $this->month_end_date . ' 23:59:59';
	}
	
	/**
	 * Get the number of calls from a site for a given client for the current month
	 *
	 * @access	public
	 * @param	int site_id
	 * @return	int
	 */
	public function getCallCountBySiteForCurrentMonth($site_id)
	{
		return $this->getCallCountBySiteForPeriod($site_id, $this->month_start_date, $this->month_end_date);
	}
	
	/**
	 *
	 */
	public function getCallCountForCurrentMonth()
	{
		return $this->getCallCountForPeriod($this->month_start_date, $this->month_end_date);
	}
	
	/**
	 * Get the number of calls from a site for a given client year-to-date
	 *
	 * @access	public
	 * @param	int site_id
	 * @return	int
	 */
	public function getCallCountBySiteForYearToDate($site_id)
	{
		return $this->getCallCountBySiteForPeriod($site_id, $this->loa_start_date, $this->month_end_date);
	}
	
	/**
	 *
	 */
	public function getCallCountForYearToDate()
	{
		return $this->getCallCountForPeriod($this->loa_start_date, $this->month_end_date);
	}
	
	/**
	 * Get the number of calls from a site for a given client in a given period
	 * 
	 * @access	private
	 * @param	int site_id
	 * @param	string start_date
	 * @param	string end_date
	 * @return	int
	 */
	private function getCallCountBySiteForPeriod($site_id, $start_date, $end_date)
	{
		$sql = "
			SELECT
				count(1) as num_calls
			FROM
				client_phone_leads
			WHERE
				client_id = {$this->client_id}
				AND site_id = {$site_id}
				AND date BETWEEN '{$start_date}' AND '{$end_date}'
		";

		$num_calls = $this->query($sql);
		return $num_calls[0][0]['num_calls'];
	}
	
	/**
	 *
	 */
	private function getCallCountForPeriod($start_date, $end_date)
	{
		$sql = "
			SELECT
				count(1) as num_calls
			FROM
				client_phone_leads
			WHERE
				client_id = {$this->client_id}
				AND date BETWEEN '{$start_date}' AND '{$end_date}'
		";
		
		$num_calls = $this->query($sql);
		return (is_null($num_calls[0][0]['num_calls'])) ? 0 : $num_calls[0][0]['num_calls'];
	}
	
	/**
	 *
	 */
	public function getEmailCountBySiteForCurrentMonth($site_id)
	{
		return $this->getEmailCountBySiteForPeriod($site_id, $this->month_start_date, $this->month_end_date);
	}
	
	/**
	 *
	 */
	public function getEmailCountBySiteForYearToDate($site_id)
	{
		return $this->getEmailCountBySiteForPeriod($site_id, $this->loa_start_date, $this-month_>end_date);
	}
	
	/**
	 *
	 */
	private function getEmailCountBySiteForPeriod($site_id, $start_date, $end_date)
	{
		$table = '';
		switch($site_id) {
			case 1: $table = 'carConsolidatedView'; break;
			case 2: $table = 'carConsolidatedViewFg'; break;
		}

		$this->setDataSource('reporting');
		$sql = "
			SELECT
				sum(email) as num_emails
			FROM
				$table
			WHERE
				clientid = {$this->client_id}
				AND activityStart BETWEEN '{$start_date}' AND '{$end_date}'
		";
		$num_emails = $this->query($sql);

		$this->setDataSource('default');
		return (is_null($num_emails[0][0]['num_emails'])) ? 0 : $num_emails[0][0]['num_emails'];
	}
	
	/**
	 *
	 */
	public function getImpressionDataBySiteForCurrentMonth($site_id)
	{
		return $this->getImpressionsBySiteForPeriod($site_id, $this->month_start_date, $this->month_end_date);
	}
	
	/**
	 *
	 */
	public function getImpressionDataBySiteForYearToDate($site_id)
	{
		return $this->getImpressionsBySiteForPeriod($site_id, $this->loa_start_date, $this->month_end_date);
	}
	
	/**
	 *
	 */
	private function getImpressionsBySiteForPeriod($site_id, $start_date, $end_date)
	{
		$table = '';
		switch($site_id) {
			case 1: $table = 'carConsolidatedView'; break;
			case 2: $table = 'carConsolidatedViewFg'; break;
		}
		
		$this->setDataSource('reporting');
		$sql = "
			SELECT
				sum(totalImpressions) as impressions,
				sum(webrefer) as clicks
			FROM
				$table
			WHERE
				clientid = {$this->client_id}
				AND activityStart BETWEEN '{$start_date}' AND '{$end_date}'
		";
		$data = $this->query($sql);
		
		$this->setDataSource('default');
		return array(
			'impressions' => (is_null($data[0][0]['impressions'])) ? 0 : $data[0][0]['impressions'],
			'clicks' => (is_null($data[0][0]['clicks'])) ? 0 : $data[0][0]['clicks']
		);
	}
	
	/**
	 * 
	 */
	public function getBookingInformation()
	{
		$booking_information = array();
		$tables = array(
			'Luxury Link' => 'offerLuxuryLink',
			'Family Getaway' => 'offerFamily'
		);

		foreach($tables as $site => $table) {
			// Get current month data
			$booking_data = $this->getBookingInformationForPeriod($table, $this->month_start_date, $this->month_end_date);
			$booking_information[$site]['current_month'] = self::buildBookingInformationArray($booking_data[0][0]['bookings'], $booking_data[0][0]['room_nights'], $booking_data[0][0]['gross_bookings']);
			
			// Get year-to-date data
			$booking_data = $this->getBookingInformationForPeriod($table, $this->loa_start_date, $this->month_end_date);
			$booking_information[$site]['year_to_date'] = self::buildBookingInformationArray($booking_data[0][0]['bookings'], $booking_data[0][0]['room_nights'], $booking_data[0][0]['gross_bookings']);
		}
		
		// Get Vacationist Bookings
		// Get current month data
		$booking_data = $this->getVacationistBookingInformationForPeriod($this->month_start_date, $this->month_end_date);
		$booking_information['Vacationist']['current_month'] = self::buildBookingInformationArray($booking_data[0][0]['bookings'], $booking_data[0][0]['room_nights'], $booking_data[0][0]['gross_bookings']);
		
		// Get year-to-date data
		$booking_data = $this->getVacationistBookingInformationForPeriod($this->loa_start_date, $this->month_end_date);
		$booking_information['Vacationist']['year_to_date'] = self::buildBookingInformationArray($booking_data[0][0]['bookings'], $booking_data[0][0]['room_nights'], $booking_data[0][0]['gross_bookings']);

		return $booking_information;
	}
	
	/**
	 *
	 */
	private function getBookingInformationForPeriod($table, $start_date, $end_date)
	{
		$sql = "
			SELECT
				count(distinct Ticket.ticketId) as bookings,
				sum(Offer.roomNights) as room_nights,
				sum(Ticket.billingPrice) as gross_bookings
			FROM
				ticket Ticket,
				paymentDetail PaymentDetail,
				{$table} Offer
			WHERE
				Offer.clientId = {$this->client_id}
				AND Ticket.offerId = Offer.offerId
				AND Ticket.ticketStatusId != 8
				AND PaymentDetail.ticketId = Ticket.ticketId
				AND PaymentDetail.isSuccessfulCharge = 1
				AND Ticket.created BETWEEN '{$start_date}' AND '{$end_date}'
		";
			
		return $this->query($sql); 
	}
	
	private function getVacationistBookingInformationForPeriod($start_date, $end_date)
	{
		$this->setDataSource('vacationist');
		// Get current month data
		$sql = "
			SELECT
				count(distinct Ticket.id) as bookings,
				sum(Ticket.numNights) as room_nights,
				sum(Ticket.salePrice) as gross_bookings
			FROM
				ticket Ticket,
				client Client
			WHERE
				Ticket.clientId = Client.id
				AND Ticket.ticketStatusTypeId = 2
				AND Client.toolboxClientId = {$this->client_id}
				AND Ticket.created between '{$start_date}' AND '{$end_date}'
		";
		$booking_data = $this->query($sql);
		$this->setDataSource('default');
		
		return $booking_data;	
	}
	
	/**
	 *
	 */
	private static function buildBookingInformationArray($bookings, $room_nights, $gross_bookings)
	{
		return array(
			'bookings' => (is_null($bookings)) ? 0 : $bookings,
			'room_nights' => (is_null($room_nights)) ? 0 : $room_nights,
			'gross_bookings' => (is_null($gross_bookings)) ? 0 : $gross_bookings,
		);		
	}
	
	/**
	 *  
	 */
	public function getImpressions()
	{
		$this->setDataSource('reporting');
		$impressions = array();
		$tables = array(
			'Luxury Link' => 'carConsolidatedView',
			'Family Getaway' => 'carConsolidatedViewFg'
		);

		foreach($tables as $site => $table) {
			$sql = "
				SELECT
					year2,
					month2,
					clientid,
					phone,
					webrefer,
					productview,
					searchview,
					destinationview,
					email,
					totalimpressions
				FROM $table
				WHERE
					clientid = {$this->client_id}					
					AND (
						activityStart BETWEEN '{$this->loa_start_date}' AND '{$this->month_end_date}'
						OR activityEnd BETWEEN '{$this->loa_start_date}' AND '{$this->month_end_date}'
					)
				ORDER BY activityStart, year2, month2
			";
			$rows = $this->query($sql);

			foreach($rows as $row) {
				$impressions[$site][][$row[$table]['month2']] = array(
					'year' => $row[$table]['year2'],
					'month' => $row[$table]['month2'],
					'phonecalls' => (is_null($row[$table]['phone'])) ? 0 : $row[$table]['phone'],
					'webrefer' => (is_null($row[$table]['webrefer'])) ? 0 : $row[$table]['webrefer'],
					'productview' => (is_null($row[$table]['productview'])) ? 0 : $row[$table]['productview'],
					'searchview' => (is_null($row[$table]['searchview'])) ? 0 : $row[$table]['searchview'],
					'destinationview' => (is_null($row[$table]['destinationview'])) ? 0 : $row[$table]['destinationview'],
					'email' => (is_null($row[$table]['email'])) ? 0 : $row[$table]['email'],
					'total_impressions' => (is_null($row[$table]['totalimpressions'])) ? 0 : $row[$table]['totalimpressions'] 
				);
			}
		}

		$this->setDataSource('default');
		return $impressions;
	}
	
	/**
	 * Query the database for contact details
	 * 
	 * Contact Types:
	 * 1. Booking
	 * 2. Call
	 * 3. Email
	 * 4. Lead
	 * 
	 * @access	public
	 * @return	array 
	 */
	public function getContactDetails()
	{
		$call_details = array();
		$booking_details = array();
		$contact_details = array();
		
		$call_details = $this->getCallDetails();
		$booking_details = array_merge($this->getBookingDetails(1), $this->getBookingDetails(2), $this->getVacationistBookingDetails());
		uasort($booking_details, array('self', 'cmp_activity_dates'));
		$contact_details = array_merge($booking_details, $call_details);
		unset($booking_details, $call_details);

		if ($this->validates()) {
			return $contact_details;
		}
	}
	
	private function getCallDetails()
	{
		$call_details_raw = array();
		$call_details = array();
		$siteName = '';
		$sql = "
			SELECT
				ClientPhoneLead.site_id,
				ClientPhoneLead.date,
				ClientPhoneLead.duration,
				ClientPhoneLead.caller_number,
				ClientPhoneLead.caller_name,
				ClientPhoneLead.caller_location
			FROM client_phone_leads ClientPhoneLead
			WHERE
				client_id = {$this->client_id}
				AND date BETWEEN '{$this->loa_start_date}' AND '{$this->month_end_date}'
				AND substr(ClientPhoneLead.caller_number,1,7) != '1424835'
			ORDER BY date DESC
		";
		
		$call_details_raw = $this->query($sql);
		foreach($call_details_raw as $key => $call_detail) {
			switch($call_detail['ClientPhoneLead']['site_id']) {
				case 1:
					$siteName = 'Luxury Link';
					break;
				case 2:
					$siteName = 'Family';
					break;
				default:
					$siteName = ''; 
			}
			
			$call_details[] = $this->buildContactDetails(
				'Call',
				$siteName,
				date('Y-m-d', strtotime($call_detail['ClientPhoneLead']['date'])),
				null,
				null,
				null,
				$call_detail['ClientPhoneLead']['duration'],
				null,
				$call_detail['ClientPhoneLead']['caller_number'],
				$call_detail['ClientPhoneLead']['caller_name'],
				null,
				null,
				null,
				$call_detail['ClientPhoneLead']['caller_location'],
				null,
				null,
				null,
				null,
				null,
				null,
				null
			);
		}
		
		return $call_details;
	}
	
	/**
	 * 
	 */
	private function getBookingDetails($site_id)
	{
		$offer_join_table = '';
		$booking_details_raw = array();
		$booking_details = array();
		$address = '';
		$city = '';
		$state = '';
		$zip = '';
		$country = '';

		switch($site_id) {
			case 1:
				$offer_join_table = 'offerLuxuryLink';
				break;
			case 2:
				$offer_join_table = 'offerFamily';
				break;
		}

		$sql = "
			SELECT
				Ticket.ticketId,
				Site.siteName,
				Ticket.created,
				Reservation.arrivalDate,
				Reservation.departureDate,
				Ticket.numNights,
				Ticket.billingPrice,
				Ticket.bidId,
				Ticket.userHomePhone,
				Ticket.userFirstName,
				Ticket.userLastName,
				Ticket.userEmail1,
				`User`.doNotContact,
				Ticket.userAddress1,
				Ticket.userAddress2,
				Ticket.userAddress3,
				Ticket.userCity,
				Ticket.userState,
				Ticket.userZip,
				Ticket.userCountry,
				PaymentDetail.ppBillingAddress1,
				PaymentDetail.ppBillingCity,
				PaymentDetail.ppBillingState,
				PaymentDetail.ppBillingZip,
				PaymentDetail.ppBillingCountry
			FROM
				ticket Ticket,
				paymentDetail PaymentDetail,
				$offer_join_table Offer,
				sites Site,
				reservation Reservation,
				`user` `User`
			WHERE
				PaymentDetail.ticketID = Ticket.ticketId
				AND Site.siteId = Ticket.siteId
				AND Ticket.offerId = Offer.offerId
				AND Reservation.ticketId = Ticket.ticketId
				AND `User`.userId = Ticket.userId
				AND PaymentDetail.isSuccessfulCharge = 1
				AND Ticket.siteId = $site_id
				AND Offer.clientId = {$this->client_id}
				AND Ticket.created between '{$this->loa_start_date}' AND '{$this->month_end_date}'
		";
		
		$booking_details_raw = $this->query($sql);
		foreach($booking_details_raw as $key => $booking_detail) {
			$address = (isset($booking_detail['Ticket']['userAddress1'])) ? $booking_detail['Ticket']['userAddress1'] . ' '. $booking_detail['Ticket']['userAddress2'] . ' ' . $booking_detail['Ticket']['userAddress3'] : $booking_detail['PaymentDetail']['ppBillingAddress1'];
			$city = (isset($booking_detail['Ticket']['userAddress1'])) ? $booking_detail['Ticket']['userCity'] : $booking_detail['PaymentDetail']['ppBillingCity'];
			$state = (isset($booking_detail['Ticket']['userAddress1'])) ? $booking_detail['Ticket']['userState'] : $booking_detail['PaymentDetail']['ppBillingState'];
			$zip = (isset($booking_detail['Ticket']['userAddress1'])) ? $booking_detail['Ticket']['userZip'] : $booking_detail['PaymentDetail']['ppBillingZip'];
			$country = (isset($booking_detail['Ticket']['userAddress1'])) ? $booking_detail['Ticket']['userCountry'] : $booking_detail['PaymentDetail']['ppBillingCountry'];
			
			$booking_details[] = $this->buildContactDetails(
				'Booking',
				$booking_detail['Site']['siteName'],
				date('Y-m-d', strtotime($booking_detail['Ticket']['created'])),
				$booking_detail['Reservation']['arrivalDate'],
				$booking_detail['Reservation']['departureDate'],
				$booking_detail['Ticket']['numNights'],
				null,
				(is_null($booking_detail['Ticket']['bidId'])) ? 'Fixed Price' : 'Auction',
				$booking_detail['Ticket']['userHomePhone'],
				$booking_detail['Ticket']['userFirstName'],
				$booking_detail['Ticket']['userLastName'],
				$booking_detail['Ticket']['userEmail1'],
				$booking_detail['User']['doNotContact'],
				$address,
				$city,
				$state,
				$zip,
				$country,
				null,
				null,
				null
			);
		}

		return $booking_details;
	}

	/**
	 * Get booking details for Vacationist
	 * 
	 * @access	private
	 * @return	array
	 */
	private function getVacationistBookingDetails()
	{
		$booking_details_raw = array();
		$booking_details = array();
		
		// Switch to vacationist database for vacationist ticket info
		$this->setDataSource('vacationist');
		
		$sql = "
			SELECT
				Ticket.id,
				Ticket.created,
				Ticket.checkIn,
				Ticket.checkOut,
				Ticket.numNights,
				Ticket.salePrice,
				`User`.firstName,
				`User`.lastName,
				`User`.zip,
				`User`.email
			FROM
				ticket Ticket,
				client Client,
				`user` `User`
			WHERE
				Client.id = Ticket.clientId
				AND User.id = Ticket.userId
				AND Client.toolboxClientId = {$this->client_id}
				AND Ticket.created BETWEEN '{$this->loa_start_date}' AND '{$this->month_end_date}'
		";
		
		$booking_details_raw = $this->query($sql);
		foreach($booking_details_raw as $key => $booking_detail) {
			$booking_details[] = $this->buildContactDetails(
				'Booking',
				'Vacationist',
				date('Y-m-d', strtotime($booking_detail['Ticket']['created'])),
				$booking_detail['Ticket']['checkIn'],
				$booking_detail['Ticket']['checkOut'],
				$booking_detail['Ticket']['numNights'],
				null,
				'Room Only',
				null,
				$booking_detail['User']['firstName'],
				$booking_detail['User']['lastName'],
				$booking_detail['User']['email'],
				null,
				null,
				null,
				null,
				$booking_detail['User']['zip'],
				null,
				null,
				null,
				null
			);
		}

		// Switch back to default database
		$this->setDataSource('default');
		return $booking_details;
	}

	/**
	 * Build contact details array
	 * 
	 * @access	private
	 * @param	string lead type
	 * @param	string site
	 * @param	string activity date
	 * @param	string arrival
	 * @param	string departure
	 * @param	int room nights
	 * @param	string booking amount
	 * @param	string call duration
	 * @param	string booking type
	 * @param	string phone number
	 * @param	string firstname
	 * @param	string lastname
	 * @param 	string email
	 * @param	string optin
	 * @param	string address
	 * @param	string city
	 * @param	string state
	 * @param	string zip
	 * @param	string country
	 * @param	string median_household_incomeper_capita_income, $median_earnings
	 * @param	string per_capita_income
	 * @param	string median_earnings
	 * 
	 * @return	array
	 */
	private function buildContactDetails($lead_type, $site, $activity_date, $arrival, $departure, $room_nights, $call_duration, $booking_type, $phone, $firstname, $lastname, $email, $optin, $address, $city, $state, $zip, $country, $median_household_income, $per_capita_income, $median_earnings)
	{
		$phone = preg_replace('[\D]', '', $phone);
		if ($phone[0] == 1) {
			$phone = substr($phone, 1);
		}
		
		$arrival = (!is_null($arrival)) ? date('j-M-y', strtotime(trim($activity_date))) : null;
		$departure = (!is_null($departure)) ? date('j-M-y', strtotime(trim($departure))) : null;
		
		return array(
			'Lead Type'					=> trim($lead_type),
			'Site'						=> trim($site),
			'Activity Date'				=> date('j-M-y', strtotime(trim($activity_date))),
			'This Month'				=> ((substr($this->report_date,0,7) === substr($activity_date,0,7))) ? 'x' : '',
			'Arrival'					=> $arrival,
			'Departure'					=> $departure,
			'Room Nights'				=> trim($room_nights),
			'Call Duration'				=> trim($call_duration),
			'Booking Type'				=> trim($booking_type),
			'Phone'						=> $phone,
			'Firstname'					=> trim($firstname),
			'Lastname'					=> trim($lastname),
			'Email'						=> trim($email),
			'Opt-in'					=> trim($optin),
			'Address'					=> trim($address),
			'City'						=> trim($city),
			'State'						=> trim($state),
			'Zip'						=> trim($zip),
			'Country'					=> trim($country)
		);
	}
	
	/**
	 * Sort method to compare activity dates
	 * 
	 * @access	private
	 * @param	array
	 * @param	array
	 * 
	 * @return	int
	 */
	private static function cmp_activity_dates($a, $b)
	{
		$a['Activity Date'] = date('Y-m-d', strtotime($a['Activity Date']));
		$b['Activity Date'] = date('Y-m-d', strtotime($b['Activity Date']));

		if ($a['Activity Date'] == $b['Activity Date']) {
			return 0;
		}
		return ($a['Activity Date'] < $b['Activity Date']) ? 1 : -1;
	}
}
?>