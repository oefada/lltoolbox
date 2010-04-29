<?php
class ClientDestinationRel extends AppModel {

	var $name = 'ClientDestinationRel';
	var $useTable = 'clientDestinationRel';
	var $primaryKey = 'clientDestinationRelId';
	
	var $belongsTo = array('Client' => array('foreignKey' => 'clientId'),
				     'Destination' => array('foreignKey' => 'destinationId'));
	
	var $deleteFirst = true;
    
    function getParentDestination($clientId, $destinationId=null) {
        $this->contain('Destination');
        $conditions = array('Destination.parentId IS NULL');
        if ($destinationId) {
            array_push($conditions, 'Destination.destinationId = '.$destinationId);
        }
        else {
            array_push($conditions, 'ClientDestinationRel.clientId = '.$clientId);
        }
        $clientDestination = $this->find('first', array('conditions' => $conditions));
        if (empty($clientDestination)) {
            if ($destinationId) {
                $conditionsAll = array('Destination.destinationId = '.$destinationId);
            }
            else {
                $conditionsAll = array('ClientDestinationRel.clientId' => $clientId);
            }
            $clientDestinations = $this->find('all', array('conditions' => $conditionsAll));
            if ($clientDestinations) {
                foreach ($clientDestinations as $destination) {
                    $parentDestination = $this->getParentDestination($clientId, $destination['Destination']['parentId']);
                    if ($parentDestination) {
                        return $parentDestination;
                    }
                    else {
                        return false;
                    }
                }
            }   
        }
        else {
            return $clientDestination['Destination']['destinationId'];
        }
    }
	
}
?>
