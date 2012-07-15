<?php
App::import('Model', 'LltUserEvent');
App::import('Model', 'ConsolidatedReportJob');
App::import('Model', 'lltUserEventRollup');
class SkynetRollupShell extends Shell
{
	private $lltUserEvent;
	private $ConsolidatedReportJob;
	private $lltUserEventRollup;
	private $logfile = 'skynet_rollup';
	
	public function initialize()
	{
		$this->lltUserEvent = new LltUserEvent;
		$this->ConsolidatedReportJob = new ConsolidatedReportJob;
		$this->lltUserEventRollup = new lltUserEventRollup;
	}
	
	public function main()
	{
		$startDate = '2012-06-01 00:00:00';
		$endDate = '2012-06-30 23:59:59';
		$siteId = 1;
		$clients = $this->ConsolidatedReportJob->getClientIdsByJobId(9);
		$rollupData = array();

		foreach($clients as $client) {
			$rollupData = $this->lltUserEvent->eventsByClient($client['client_id'], $siteId, $startDate, $endDate);
			if (!empty($rollupData)) {
				$numRecords = sizeof($rollupData);
				$this->lltUserEventRollup->create();
				if ($this->lltUserEventRollup->saveAll($rollupData)) {
					$this->log("Saved $numRecords records for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
				} else {
					$this->log("Could not save $numRecords records for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
				}
			} else {
				$this->log("No rollup data for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
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
		parent::log($message, $this->logfile);
		echo date('Y-m-d H:i:s') . ' - ' . $message . "\n";
	}
}
