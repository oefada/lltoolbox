<?php
class LltUserEvent extends AppModel
{
	public $name = 'LltUserEvent';
	public $useDbConfig = 'business_db2';
	public $useTable = 'lltUserEvent';
	
	/**
	 * Gets a rollup of events within a specified date
	 * period for given client id and site id
	 * 
	 * @param	int $clientId
	 * @param	int $siteId
	 * @param	string $startDate
	 * @param	string $endDate
	 * @return	array
	 */
	public function eventsByClient($clientId, $siteId, $startDate, $endDate)
	{
		$query = "
			SELECT
				SUBSTRING(`lltue`.`dateCreated`, 1, 10) as `dateCreated`,
				`lltue`.`lltEventId`,
				count(1) as `total`
			FROM
				`lltUserEvent` `lltue` USE INDEX (`eventRelatedClient`)
			WHERE
				`lltue`.`eventRelatedClient` = ?
				AND `lltue`.`siteId` = ?
				AND `lltue`.`dateCreated` BETWEEN ? AND ?
			GROUP BY
				SUBSTRING(`lltue`.`dateCreated`, 1, 10),
				`lltue`.`lltEventId`;
		";
		$params = array($clientId, $siteId, $startDate, $endDate);
		
		$results = $this->query($query, $params);
		$dataToReturn = array();
		
		if (!empty($results)) {
			foreach($results as $result) {
				$dataToReturn[] = array(
					'clientId'	=> $clientId,
					'siteId'	=> $siteId,
					'eventId'	=> $result['lltue']['lltEventId'],
					'startDate'	=> $result[0]['dateCreated'] . ' 00:00:00',
					'endDate'	=> $result[0]['dateCreated'] . ' 23:59:59',
					'total'		=> $result[0]['total'],
					'created'	=> date('Y-m-d H:i:s')
				);
			}
		}

		return $dataToReturn;
	}
}
