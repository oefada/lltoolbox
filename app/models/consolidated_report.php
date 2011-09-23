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