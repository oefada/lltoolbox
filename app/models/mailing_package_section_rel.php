<?php
class MailingPackageSectionRel extends AppModel {

	var $name = 'MailingPackageSectionRel';
	var $useTable = 'mailingPackageSectionRel';
	var $primaryKey = 'mailingPackageSectionRelId';
    
    var $belongsTo = array('Mailing' => array('className' => 'Mailing', 'foreignKey' => 'mailingId'),
                           'MailingSection' => array('className' => 'MailingSection', 'foreignKey' => 'mailingSectionId'),
                           'Package' => array('className' => 'Package', 'foreignKey' => 'packageId'),
                           'Loa' => array('className' => 'Loa', 'foreignKey' => 'loaId'),
                           'Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));

    function getAvailableClients($conditions, $mailingId, $sectionId, $variationId) {
        $query = "SELECT Client.clientId, Client.name FROM client AS Client
                    INNER JOIN loa AS Loa USING(clientId)
                    WHERE LOWER(Client.name) LIKE '{$conditions}%'
                    AND Loa.numEmailInclusions > 0
                    AND Loa.numEmailInclusions > Loa.numEmailsSent
                    AND Loa.loaLevelId = 2
                    AND now() BETWEEN Loa.startDate AND Loa.endDate
                    AND Loa.inactive = 0
                    AND Client.clientId NOT IN (
                        SELECT clientId from mailingPackageSectionRel
                        WHERE mailingId = {$mailingId} AND
                              (mailingSectionId != {$sectionId} OR
                              (mailingSectionId = {$sectionId} AND variation = '{$variationId}'))
                    )
                    ORDER BY Client.name";
        $results = $this->query($query);
        return $results;
    }
    
    function saveClient($data) {
        $data['decrementLoaNumInclusions'] = $this->MailingSection->field('MailingSection.loaFulfillment', array('MailingSection.mailingSectionId' => $data['mailingSectionId']));
        $sortOrder = $this->find('count', array('conditions' => array('MailingPackageSectionRel.mailingId' => $data['mailingId'], 'MailingPackageSectionRel.mailingSectionId' => $data['mailingSectionId'], 'MailingPackageSectionRel.variation' => $data['variation'])))+1;
        if (empty($sortOrder)) {
            $sortOrder = 1;
        }
        $data['destinationId'] = $this->Client->ClientDestinationRel->getParentDestination($data['clientId']);
        $data['sortOrder'] = $sortOrder;
        $this->create();
        return $this->save($data);
    }
}
?>