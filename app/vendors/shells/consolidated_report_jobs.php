<?php
class ConsolidatedReportJobsShell extends Shell
{
	public $uses = array('ConsolidatedReportJob');
	public $errors = array();
	public static $logfile = 'consolidated_report_jobs';
	
	public function initialize() {
		self::log('Consolidated Job Process Started.');
		parent::initialize();
	}
	
	public function __destruct() {
		self::log('Consolidated Job Process Completed.');
	}
	
	public function main() {
		$jobs = $this->ConsolidatedReportJob->getScheduled();

		$client_id = null;
		$cake_command = CAKE . 'console' . DS . 'cake';
		$command = null;
		
		self::log("Number of jobs: " . sizeof($jobs));
		
		foreach($jobs as $job) {
			$job_id = $job['ConsolidatedReportJob']['id'];
			$report_date = $job['ConsolidatedReportJob']['report_date'];
			$tasks = $job['ConsolidatedReportJobsClients'];
			$is_production = (isset($this->params['production'])) ? true : false;
			$useSkynetData = (isset($this->params['useSkynetData'])) ? true : false;
			
			$this->ConsolidatedReportJob->setJobInProgress($job_id);
			
			self::log("Current job id: $job_id");
			self::log("Report date for current job: $report_date");
			self::log('Tasks in job: ' . sizeof($tasks));
			
			foreach($tasks as $task) {
				if ($task['status'] === 'scheduled') {
					$client_id = $task['client_id'];
					$command = "consolidated_report -report_date $report_date -client_id $client_id";
					if ($is_production === true) {
						$command .= ' -production';
					}
					if ($useSkynetData === true) {
						$command .= ' -useSkynetData';
					}

					$output = array();
					$status = 0;

					self::log("Creating report for client_id: $client_id.");
					self::log("Invoking command: '$command'");
					exec($cake_command . ' ' . $command, $output, $status);

					if ($status === 0) {
						$this->ConsolidatedReportJob->setTaskCompleted($task['id']);
						self::log("Successfully generated report for client_id: $client_id.");
					} else {
						$error_msg = "There was an error generating a report for client_id: $client_id.";
						$this->errors[] = $error_msg;
						self::log($error_msg);
					}
				}
			}
			
			if (empty($this->errors)) {
				$this->ConsolidatedReportJob->setJobCompleted($job_id);
				self::log("Completed job id: $job_id");
			}
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
}
?>