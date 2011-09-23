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
	public function getBookingInformation()
	{
		
	}
	
	/**
	 * 
	 */
	public function getImpressions()
	{
		
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
		$bookin_details = array();
		$contact_details = array();
		
		$call_details_query = "
			SELECT *
			FROM client_phone_leads
			WHERE
				client_id = {$this->client_id}
				AND date BETWEEN '{$this->start_date}' AND '{$this->end_date}'
		";
		
		$booking_details_query = "
			SELECT oll.clientId, t.*
			FROM ticket t, paymentDetail pd, offerLuxuryLink oll
			WHERE
				pd.ticketID = t.ticketId
				AND t.offerId = oll.offerId
				AND pd.isSuccessfulCharge = 1
				AND t.siteId = 1
				AND t.created between '2011-08-01' AND '2011-08-31' 
				AND oll.clientId = 10006
		";
		
		if ($this->validates()) {
			return $this->query($call_details_query);
			
		}
	}
}
?>