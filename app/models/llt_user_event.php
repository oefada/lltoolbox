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
     * @param    int $clientId
     * @param    int $siteId
     * @param    string $startDate
     * @param    string $endDate
     * @return    array
     */
    public function eventsByClient($clientId, $siteId, $startDate, $endDate)
    {
        $query = "
			SELECT
				SUBSTRING(`lltue`.`dateCreated`, 1, 10) as `dateCreated`,
				`lltue`.`lltEventId`,
				count(1) as `total`
			FROM
				`lltUserEvent` `lltue` USE INDEX (`idx_lltUserEvent_eventRelatedClient_siteId_created`)
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
            foreach ($results as $result) {
                $dataToReturn[] = array(
                    'clientId' => $clientId,
                    'siteId' => $siteId,
                    'eventId' => $result['lltue']['lltEventId'],
                    'startDate' => $result[0]['dateCreated'] . ' 00:00:00',
                    'endDate' => $result[0]['dateCreated'] . ' 23:59:59',
                    'total' => $result[0]['total'],
                    'created' => date('Y-m-d H:i:s')
                );
            }
        }

        return $dataToReturn;
    }

    /**
     *
     */
    public function getClientsWithEventDataBetweenDatesBySiteId($startDate, $endDate, $siteId)
    {
        $sql = "
            SELECT DISTINCT(eventRelatedClient) AS clientId
            FROM lltUserEvent
            WHERE
              eventRelatedClient > 0
              AND siteId = ?
              AND dateCreated BETWEEN ? AND ?
            ORDER BY eventRelatedClient
        ";

        return $this->query($sql, array($siteId, $startDate, $endDate));
    }

    public function eventRollupBySourceId($startDate, $endDate)
    {
        $sql = '
            SELECT
              lltSourceId,
              lltEventId,
              siteId,
              count(1) as count,
              SUBSTRING(dateCreated, 1, 10) as dateCreated
            FROM
              lltUserEvent
            WHERE
              dateCreated BETWEEN ? AND ?
              AND eventRelatedClient IS NULL
            GROUP BY
              lltSourceId,
              siteId,
              lltEventId,
              SUBSTRING(dateCreated, 1, 10)
            ORDER BY dateCreated ASC
        ';

        $eventData = $this->query($sql, array($startDate, $endDate));

        $dataToReturn = array();
        if (!empty($eventData)) {
            foreach ($eventData as $result) {
                $dataToReturn[] = array(
                    'lltSourceId' => $result['lltUserEvent']['lltSourceId'],
                    'lltEventId' => $result['lltUserEvent']['lltEventId'],
                    'siteId' => $result['lltUserEvent']['siteId'],
                    'count' => $result[0]['count'],
                    'date' => $result[0]['dateCreated']
                );
            }
        }
        return $dataToReturn;
    }
}
