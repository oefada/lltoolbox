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
		if ($this->start_date >= $this->end_date) {
			$isValid = false;
		}
		
		return $isValid;
	}
	
	/**
	 * 
	 */
	public function getBookingInformation($client_id)
	{
		
	}
	
	/**
	 * 
	 */
	public function getImpressions($client_id)
	{
		
	}
	
	/**
	 * 
	 */
	public function getLeadsByGeo($client_id)
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
	 * @param	int client_id
	 * @param	string start_date
	 * @param	string end_date
	 * @return	array 
	 */
	public function getContactDetails($client_id, $start_date, $end_date)
	{
		
	}
}
?>