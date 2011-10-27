<?php
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Core', 'ConnectionManager');
App::Import('Model', 'Client');
App::Import('Model', 'Loa');
App::Import('Model', 'User');
App::Import('Model', 'ConsolidatedReport');
App::import('Vendor', 'ConsolidatedReportHelper', array('file' => 'consolidated_report' . DS . 'consolidated_report_helper.php'));
class ConsolidatedReportShell extends Shell {
	private $isProduction = false;
	
	private $Controller;
	private $Email;
	private $Client;
	private $Loa;
	private $User;
	private $ConsolidatedReport;
	
	private static $logfile = 'consolidated_report';
	private $errors = array();
	
	/**
	 *
	 */
	public function initialize()
	{
		self::log('Process Started.');
		$this->Client = new Client();
		$this->Loa = new Loa();
		$this->User = new User();
		$this->Controller = new Controller();
		$this->Email = new EmailComponent(null);
		$this->Email->startup($this->Controller);
		$this->ConsolidatedReport = new ConsolidatedReport();
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
		$this->isProduction = isset($this->params['production']) ? true : false;
		
		// Report initialization variables
		$template = 'consolidated_report_revision-7.xlsx';
		
		$report_date = isset($this->params['report_date']) ? $this->params['report_date'] : null;		
		$client_id = isset($this->params['client_id']) ? $this->params['client_id'] : null;

		// Client & LOA Details
		$client_details = $this->ConsolidatedReport->getClientDetails($client_id);
		$loa_details = $this->ConsolidatedReport->getLoaDetails($client_id, $report_date);

		if ($loa_details !== false) {
			// The client has a current LOA around the report date, generate the report
		
			if (!$this->isProduction) {
				self::log("This is not a production run. Reports will not be sent to the client.");
				$send_report_to = 'mclifford@luxurylink.com';
			} else {
				self::log("This is a production run. Reports will be sent to the client.");
				$send_report_to = $client_details['Client']['email'];
			}

			// Set date, fee, and output file parameters
			$loa_start_date = date('Y-m-d', strtotime($loa_details['Loa']['startDate']));
			$loa_end_date = date('Y-m-d', strtotime($loa_details['Loa']['endDate']));
			$membership_fee = $loa_details['Loa']['membershipFee'];
			$outputFile = TMP . "consolidated_reports/" . $client_details['Client']['seoName'] . '_' . $loa_start_date . '_to_' . $report_date . '_consolidated_report.xlsx';
		
			// Log date and filename parameters
			self::log("Generating Report for client_id: $client_id, report_date: $report_date");
			self::log("LOA Period: $loa_start_date - $loa_end_date");
			self::log("Filepath: $outputFile");
		
			// Initialize the report Model
			self::log("Initializing the report model.");
			$this->ConsolidatedReport->create($client_id, $report_date, $loa_start_date, $loa_end_date);
			self::log("The report model has been initialized successfully.");
		
			// Create the report object
			self::log("Creating a blank report object in memory.");
			$report = new ConsolidatedReportHelper($template, $outputFile, $this->ConsolidatedReport);
			self::log("The blank report object has been created successfully.");
		
			// Populate the report
			self::log('Building the report data array.');
			$report->populateDashboard($client_details['Client']['name'], $membership_fee, $loa_start_date, $this->ConsolidatedReport->getMonthEndDate());		
			$report->populateActivitySummary($loa_start_date, $this->ConsolidatedReport->getMonthEndDate(), $client_details['AccountManager']['name'], $client_details['AccountManager']['email'], 15, 2);
			$report->populateBookings();
			$report->populateImpressions();
			$report->populateContactDetails();
			self::log('The report data array was built successfully.');

			// Save array to spreadsheet object
			try {
				self::log('Saving the report data array to the report object.');
				$report->populateFromArray($report->getDataToPopulate());
				self::log('The save was successful.');

				// Write the report object to disk
				try {
					self::log('Writing the report object to disk.');
					$report->writeSpreadsheetObjectToFile();
					self::log('The write was successful.');
					self::log("Emailing report to $send_report_to");
					//$this->emailReport($send_report_to, $client_details['Client']['name'], $report_date, $outputFile);
					return 0;
				} catch (Exception $e) {
					self::log("Error - There was an issue saving the report object to disk. Message: '" . $e->getMessage() . "'");
					return 1;
				}
			} catch (Exception $e) {
				self::log("Error - There was an issue saving the array to the spreadsheet object. Message: '" . $e->getMessage() . "'");		
				return 1;
			}
		} else {
			self::log("This client doesn't have a current LOA within the report period.");
			return 1;
		}
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
	 * Email the generated report
	 *
	 * @access	private
	 * @param	string to address
	 * @param	string client name
	 * @param	string path to file to attach
	 */
	private function emailReport($recipient, $client_name, $report_date, $file)
	{
		$this->Email->reset();
		$this->Email->from = 'noreply@luxurylink.com';
		$this->Email->to = 'mclifford@luxurylink.com';
		$this->Email->subject = "Consolidated Report for $client_name, $report_date";
		$this->Email->attachments = array($file);
		$this->Email->send();
	}
}
?>