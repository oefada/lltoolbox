<?php
App::import('Model', 'LltUserEvent');
App::import('Model', 'ConsolidatedReportJob');
App::import('Model', 'lltUserEventRollup');
class SkynetRollupShell extends Shell
{
	private $lltUserEvent;
	private $ConsolidatedReportJob;
	private $lltUserEventRollup;
	
	public function initialize()
	{
		$this->lltUserEvent = new LltUserEvent;
		$this->ConsolidatedReportJob = new ConsolidatedReportJob;
		$this->lltUserEventRollup = new lltUserEventRollup;
	}
	
	public function main()
	{
		$startDate = '2012-05-01 00:00:00';
		$endDate = '2012-05-31 23:59:59';
		$siteId = 1;
		$clients = $this->ConsolidatedReportJob->getClientIdsByJobId(8);
		$rollupData = array();
		
		foreach($clients as $client) {
			$rollupData = $this->lltUserEvent->eventsByClient($client['client_id'], $siteId, $startDate, $endDate);
			if (!empty($rollupData)) {
				var_dump($this->lltUserEventRollup->saveAll($rollupData));
			}
		}
	}
}
