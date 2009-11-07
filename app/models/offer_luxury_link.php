<?php
class OfferLuxuryLink extends AppModel {

	var $name = 'OfferLuxuryLink';
	var $useTable = 'offerLuxuryLink';
	var $primaryKey = 'offerId';
	var $actsAs = array('Logable');
	
	var $validate = array(
						'endDate' => array('rule' => 
													array('validateDateRanges'),
													'message' => 'Must be greater than today and greater than the start date'
												),
						'offerName' => array('rule' =>
						                            array('checkIfOfferIsLive'),
						                            'message' => 'This offer has closed and cannot be edited')
						);
						
	function validateDateRanges($data) {
	    $offerLuxuryLink = $this->find('first', array('conditions' => array('OfferLuxuryLink.offerId' => $this->data['OfferLuxuryLink']['offerId'])));

        $startDate = $offerLuxuryLink['OfferLuxuryLink']['startDate'];
        $newEndDate = $this->data['OfferLuxuryLink']['endDate'];
    
        if (strtotime($startDate) >= strtotime($newEndDate) || ($offerLuxuryLink['OfferLuxuryLink']['endDate'] != $newEndDate && time() >= strtotime($newEndDate))) return false;

        return true;
    }
    
    function checkIfOfferIsLive($data) {
	    $offerLuxuryLink = $this->find('first', array('conditions' => array('OfferLuxuryLink.offerId' => $this->data['OfferLuxuryLink']['offerId'])));
    
        if ($offerLuxuryLink['OfferLuxuryLink']['isClosed']) return false;

        return true;
    }
    
    function afterSave($created) {
        //TODO: update instance with new date
        if (!$created && !empty($this->data['OfferLuxuryLink']['endDate'])) {
            $results = $this->query("SELECT schedulingInstanceId, endDate FROM schedulingInstance as SchedulingInstance INNER JOIN offer USING(schedulingInstanceId) WHERE offerId = ".$this->data['OfferLuxuryLink']['offerId']);
            $results = $results[0];
            if($results['SchedulingInstance']['endDate'] != $this->data['OfferLuxuryLink']['endDate']) {
                $this->query("UPDATE schedulingInstance SET endDate = '{$this->data['OfferLuxuryLink']['endDate']}' WHERE schedulingInstanceId = ".$results['SchedulingInstance']['schedulingInstanceId']); 
            }
        }
        return true;
    }
}
?>
