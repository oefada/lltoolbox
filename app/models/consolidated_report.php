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
class ConsolidatedReport extends AppModel
{
	public $useTable = false;
	
	/**
	 * client id
	 * 
	 * @access	private
	 * @param	int
	 */
	private $client_id;
	
	/**
	 * start date
	 * 
	 * @access	private
	 * @param	int
	 */
	private $start_date;
	
	/**
	 * end date
	 * 
	 * @access	private
	 * @param	int
	 */
	private $end_date;
	
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
	public function create($client_id, $start_date, $end_date)
	{
		$this->client_id = $client_id;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		
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
		} else if (!isset($this->start_date)) {
			$isValid = false;
		} else if (!isset($this->end_date)) {
			$isValid = false;
		} else if ($this->start_date >= $this->end_date) {
			$isValid = false;
		}
		
		return $isValid;
	}
	
	/**
	 * 
	 */
	public function getClientDetails()
	{
		
	}
	
	/**
	 * 
	 */
	public function getBookingInformation()
	{
		
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
		$start_date = date('Y', strtotime($this->start_date)) . '-01-01';

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
					AND activityStart >= '{$start_date}'
					AND activityEnd < '{$this->end_date}'
				ORDER BY year2, month2
			";
			
			// TODO: Remove after testing
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
				ORDER BY year2, month2
			";

			$rows = $this->query($sql);
			foreach($rows as $row) {
				$impressions[$site][$row[$table]['month2']] = array(
					'year' => $row['carConsolidatedView']['year2'],
					'month' => $row['carConsolidatedView']['month2'],
					'phonecalls' => $row['carConsolidatedView']['phone'],
					'webrefer' => $row['carConsolidatedView']['webrefer'],
					'productview' => $row['carConsolidatedView']['productview'],
					'searchview' => $row['carConsolidatedView']['searchview'],
					'destinationview' => $row['carConsolidatedView']['destinationview'],
					'email' => $row['carConsolidatedView']['email'],
					'total_impressions' => $row['carConsolidatedView']['totalimpressions']
				);
			}
		}
		$this->setDataSource('default');
		return $impressions;
	}
	
	/**
	 * 
	 */
	public function getLeadsByGeo()
	{
		
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
		$booking_details = array_merge($this->getBookingDetails(1), $this->getBookingDetails(2));
		$contact_details = array_merge($call_details, $booking_details);
		unset($call_details, $booking_details);
		usort($contact_details, array('self', 'cmp_booking_dates'));
	
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
				ClientPhoneLead.caller_location,
				ClientPhoneLead.country,
				ClientPhoneLead.median_household_income,
				ClientPhoneLead.per_capita_income,
				ClientPhoneLead.median_earnings
			FROM client_phone_leads ClientPhoneLead
			WHERE
				client_id = {$this->client_id}
				AND date BETWEEN '{$this->start_date}' AND '{$this->end_date}'
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
			
			$call_details[] = self::buildContactDetails(
				'Call',
				$siteName,
				date('Y-m-d', strtotime($call_detail['ClientPhoneLead']['date'])),
				null,
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
				$call_detail['ClientPhoneLead']['country'],
				$call_detail['ClientPhoneLead']['median_household_income'],
				$call_detail['ClientPhoneLead']['per_capita_income'],
				$call_detail['ClientPhoneLead']['median_earnings']
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
				Ticket.userCountry
			FROM
				ticket Ticket,
				paymentDetail PaymentDetail,
				$offer_join_table Offer,
				site Site,
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
				AND Ticket.created between '{$this->start_date}' AND '{$this->end_date}'
		";
		
		$booking_details_raw = $this->query($sql);
		foreach($booking_details_raw as $key => $booking_detail) {
			$booking_details[] = self::buildContactDetails(
				'Booking',
				$booking_detail['Site']['siteName'],
				date('Y-m-d', strtotime($booking_detail['Ticket']['created'])),
				$booking_detail['Reservation']['arrivalDate'],
				$booking_detail['Reservation']['departureDate'],
				$booking_detail['Ticket']['numNights'],
				$booking_detail['Ticket']['billingPrice'],
				null,
				(is_null($booking_detail['Ticket']['bidId'])) ? 'Fixed Price' : 'Auction',
				$booking_detail['Ticket']['userHomePhone'],
				$booking_detail['Ticket']['userFirstName'],
				$booking_detail['Ticket']['userLastName'],
				$booking_detail['Ticket']['userEmail1'],
				$booking_detail['User']['doNotContact'],
				$booking_detail['Ticket']['userAddress1'] . ' '. $booking_detail['Ticket']['userAddress2'] . ' ' . $booking_detail['Ticket']['userAddress3'],
				$booking_detail['Ticket']['userCity'],
				$booking_detail['Ticket']['userState'],
				$booking_detail['Ticket']['userZip'],
				$booking_detail['Ticket']['userCountry'],
				null,
				null,
				null
			);
		}

		// Switch to vacationist database for vacationist ticket info
		$this->setDataSource('vacationist');
 		$sql = "
 			SELECT
 				Ticket.id,
 				Ticket.created,
 				Ticket.checkIn,
 				Ticket.checkOut,
 				Ticket.numNights,
 				Ticket.salePrice
 		";
		
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
	private static function buildContactDetails($lead_type, $site, $activity_date, $arrival, $departure, $room_nights, $booking_amount, $call_duration, $booking_type, $phone, $firstname, $lastname, $email, $optin, $address, $city, $state, $zip, $country, $median_household_income, $per_capita_income, $median_earnings)
	{
		return array(
			'Lead Type'					=> trim($lead_type),
			'Site'						=> trim($site),
			'Activity Date'				=> trim($activity_date),
			'Arrival'					=> trim($arrival),
			'Departure'					=> trim($departure),
			'Room Nights'				=> trim($room_nights),
			'Booking Amount'			=> trim($booking_amount),
			'Call Duration'				=> trim($call_duration),
			'Booking Type'				=> trim($booking_type),
			'Phone'						=> trim($phone),
			'Firstname'					=> trim($firstname),
			'Lastname'					=> trim($lastname),
			'Email'						=> trim($email),
			'Opt-in'					=> trim($optin),
			'Address'					=> trim($address),
			'City'						=> trim($city),
			'State'						=> trim($state),
			'Zip'						=> trim($zip),
			'Country'					=> trim($country),
			'Median Household Income'	=> trim($median_household_income),
			'Per Capita Income'			=> trim($per_capita_income),
			'Median Earnings'			=> trim($median_earnings)
		);
	}
	
	/**
	 * Sort method to compare booking dates and sort contact detail arrays
	 * 
	 * @access	private
	 * @param	array
	 * @param	array
	 * 
	 * @return	int
	 */
	private static function cmp_booking_dates($a, $b)
	{
		if ($a['Activity Date'] == $b['Activity Date']) {
			return 0;
		}
		return ($a['Activity Date'] > $b['Activity Date']) ? 1 : -1;
	}
}
?>