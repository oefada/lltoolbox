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
	private $report_date = '';
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
		$this->report_date = isset($this->params['report_date']) ? $this->params['report_date'] : null;
		$this->isProduction = isset($this->params['production']) ? true : false;
		$client_id = isset($this->params['client_id']) ? $this->params['client_id'] : null;
		
		// Report initialization variables
		$template = APP . 'vendors/consolidated_report/templates/consolidated_report_revision-7.xlsx';
		$newFile = TMP . 'consolidated_report.xlsx';
		
		// Client & LOA Details
		$client_details = $this->getClientDetails($client_id);
		$loa_details = $this->getLoaDetails($client_id);
		
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
			$outputFile = TMP .'consolidated_report_' . $client_details['Client']['seoName'] . '_' . $loa_start_date . '_to_' . $this->report_date . '.xlsx';
		
			// Log date and filename parameters
			self::log("Generating Report for client_id: $client_id, report_date: {$this->report_date}");
			self::log("LOA Period: $loa_start_date - $loa_end_date");
			self::log("Filepath: $outputFile");
		
			// Initialize the report Model
			$this->ConsolidatedReport = new ConsolidatedReport();
			$this->ConsolidatedReport->create($client_id, $this->report_date, $loa_start_date, $loa_end_date);
		
			// Create the report object
			$report = new ConsolidatedReportHelper($template, $newFile, $outputFile, $this->ConsolidatedReport);
		
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
					$this->emailReport($send_report_to, $client_details['Client']['name'], $outputFile);				
				} catch (Exception $e) {
					self::log("Error - There was an issue saving the report object to disk. Message: '" . $e->getMessage() . "'");
				}
			} catch (Exception $e) {
				self::log("Error - There was an issue saving the array to the spreadsheet object. Message: '" . $e->getMessage() . "'");		
			}
		} else {
			self::log("This client doesn't have a current LOA within the report period.");
		}
	}
	
	/**
	 * Get client details
	 * 
	 * @access	private
	 */
	private function getClientDetails($client_id)
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
	 * Get the LOA details for a given client
	 *
	 * @access	private
	 */
	private function getLoaDetails($client_id)
	{
		$loa_details = $this->Loa->find(
			'first',
			array(
				'recursive' => -1,
				'fields' => array('clientId', 'loaId', 'startDate', 'endDate', 'membershipFee'),
				'conditions' => array(
					"'{$this->report_date}' BETWEEN startDate AND endDate",
					"clientId = $client_id"
				)
			)
		);

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
	 * Email the generated report
	 *
	 * @access	private
	 * @param	string to address
	 * @param	string client name
	 * @param	string path to file to attach
	 */
	private function emailReport($recipient, $client_name, $file)
	{
		$this->Email->reset();
		$this->Email->from = 'noreply@luxurylink.com';
		$this->Email->to = 'mclifford@luxurylink.com';
		$this->Email->subject = "Consolidated Report for $client_name, {$this->report_date}";
		$this->Email->attachments = array($file);
		$this->Email->send();
	}
}
?>