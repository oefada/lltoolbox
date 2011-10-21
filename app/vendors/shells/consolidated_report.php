<?php
App::import('core', array('ConnectionManager'));
App::Import('Model', 'Client');
App::Import('Model', 'ConsolidatedReport');
App::import('Vendor', 'ConsolidatedReportHelper', array('file' => 'consolidated_report' . DS . 'consolidated_report_helper.php'));
class ConsolidatedReportShell extends Shell {
	private $client_id = '';
	private $report_date = '';
	
	private $Client;
	private $Loa;
	private $ConsolidatedReport;
	
	private static $logfile = 'consolidated_report';
	private $errors = array();
	
	private $filepath = '/tmp/';
	
	/**
	 *
	 */
	public function initialize()
	{
		self::log('Process Started.');
	}

	/**
	 * 
	 */
	public function __destruct()
	{
		self::log('Process Completed.');
	}
	
	/**
	 * Main routine
	 *
	 * @access	public
	 */
	public function main()
	{
		$this->client_id = isset($this->params['client_id']) ? $this->params['client_id'] : null;
		$this->report_date = isset($this->params['report_date']) ? $this->params['report_date'] : null;
		
		// Report initialization variables
		$template = APP . 'vendors/consolidated_report/templates/consolidated_report_revision-6.xlsx';
		$newFile = TMP . 'consolidated_report.xlsx';
		
		// Client & LOA Details
		$client_details = $this->getClientDetails();
		$loa_details = $this->getLoaDetails();

		// Set date, fee, and output file parameters
		$loa_start_date = date('Y-m-d', strtotime($loa_details['Loa']['startDate']));
		$loa_end_date = date('Y-m-d', strtotime($loa_details['Loa']['endDate']));
		$membership_fee = $loa_details['Loa']['membershipFee'];
		$outputFile = TMP .'consolidated_report_' . $client_details['Client']['seoName'] . '_' . $loa_start_date . '_to_' . $this->report_date . '.xlsx';
		
		// Log date and filename parameters
		self::log("Generating Report for client_id: {$this->client_id}, report_date: {$this->report_date}");
		self::log("LOA Start: $loa_start_date");
		self::log("LOA End: $loa_end_date");
		self::log("Filename: $outputFile");
		
		// Initialize the report Model
		$this->ConsolidatedReport = new ConsolidatedReport();
		$this->ConsolidatedReport->create($this->client_id, $this->report_date, $loa_start_date, $loa_end_date);
		
		// Create the report object
		$report = new ConsolidatedReportHelper($template, $newFile, $outputFile, $this->ConsolidatedReport);
		
		// Populate the report
		self::log("Building the report data array.");
		$report->populateDashboard($client_details['Client']['name'], $membership_fee, $loa_start_date, $this->ConsolidatedReport->getMonthEndDate());		
		$report->populateActivitySummary($loa_start_date, $this->ConsolidatedReport->getMonthEndDate(), 15, 2);
		$report->populateBookings();
		$report->populateImpressions();
		$report->populateContactDetails();
		self::log("The report data array was built successfully.");

		// Save array to spreadsheet object
		try {
			self::log("Saving the report data array to the report object.");
			$report->populateFromArray($report->getDataToPopulate());
			self::log("The save was successful.");

			// Write the report object to disk
			try {
				self::log("Writing the report object to disk.");
				$report->writeSpreadsheetObjectToFile();
				self::log("The write was successful.");
			} catch (Exception $e) {
				self::log("Error - There was an issue saving the report object to disk. Message: '" . $e->getMessage() . "'");
			}
		} catch (Exception $e) {
			self::log("Error - There was an issue saving the array to the spreadsheet object. Message: '" . $e->getMessage() . "'");		
		}
	}
	
	/**
	 * Get client details
	 * 
	 * @access	private
	 */
	private function getClientDetails()
	{
		$client = new Client();
		$client->id = $this->client_id;
		$client_details = $client->find('first', array('recursive' => -1));
		unset($client);
		
		return $client_details;
	}
	
	/**
	 * Get the LOA details for a given client
	 *
	 * @access	private
	 */
	private function getLoaDetails()
	{
		$loa = new Loa();
		$loa_details = $loa->find(
			'first',
			array(
				'recursive' => -1,
				'fields' => array('clientId', 'loaId', 'startDate', 'endDate', 'membershipFee'),
				'conditions' => array(
					"'{$this->report_date}' BETWEEN startDate AND endDate",
					"clientId = {$this->client_id}"
				)
			)
		);
		unset($loa);
		return $loa_details;
	}
	
	/**
	 * Utility method to log output
	 *
	 * @access	public
	 * @param	string message to log
	 */
	public function log($message)
	{
		parent::log($message, self::$logfile);
	}
	
	/**
	 * Utility method to send email notifications when errors crop up
	 *
	 * @access	public
	 * @param	array messages to email
	 */
	private function sendEmailNotification($messages)
	{
		$emailTo = 'mclifford@luxurylink.com';
		$emailSubject = "Error encountered in Toolbox shell - booking_report.php";
		$emailHeaders = "From: LuxuryLink.com DevMail<devmail@luxurylink.com>\r\n";
		$emailBody = "While generating the booking report for Convertro I encountered the following error(s):\r\n\r\n";
		foreach($messages as $message) {
			$emailBody .= $message . "\r\n";
		}
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);		
	}
}
?>