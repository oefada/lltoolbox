<?php
class ClientImpressions extends AppModel
{
	public $useTable = 'client_impressions';
	
	public function getThisMonthsImpressions($client_id, $source_id)
	{
		$start_date = date('Y-m') . '-01 00:00:00';
		$end_date = date('Y-m-d') . ' 23:59:59';
		
		return $this->getImpressionsForDateRange($start_date, $end_date, $client_id, $source_id);
	}
	
	/**
	 * 
	 */
	public function getThisYearsImpressions($client_id, $source_id)
	{
		$start_date = date('Y') . '-01-01 00:00:00';
		$end_date = date('Y-m-d') . ' 23:59:59';

		return $this->getImpressionsForDateRange($start_date, $end_date, $client_id, $source_id);
	}
	
	/**
	 * 
	 */
	public function getLastMonthsImpressions($client_id, $source_id)
	{
		$start_date = date('Y-m', strtotime("Last month")) . '-01 00:00:00';
		$end_date = date('Y-m') . '-01 00:00:00';
		
		return $this->getImpressionsForDateRange($start_date, $end_date, $client_id, $source_id);
	}
	
	/**
	 * 
	 */
	public function getImpressionsForDateRange($start_date, $end_date, $client_id, $source_id)
	{
		$params = array(
			'fields' => array('SUM(impressions) as impressions'),
			'conditions' => array(
				'ClientImpressions.client_id' => $client_id,
				'ClientImpressions.impression_source_id' => $source_id,
				'ClientImpressions.startDate >=' =>  $start_date,
				'ClientImpressions.endDate < ' =>  $end_date
			)
		);
		
		$impressions = $this->find('first', $params);
		if (isset($impressions[0]['impressions'])) {
			return (int) $impressions[0]['impressions'];
		} else {
			return 0;
		}
	}
}
?>