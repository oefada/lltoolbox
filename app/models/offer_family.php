<?php
class OfferFamily extends AppModel {

	var $name = 'OfferFamily';
	var $useTable = 'offerFamily';
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
	    $offerFamily = $this->find('first', array('conditions' => array('OfferFamily.offerId' => $this->data['OfferFamily']['offerId'])));

        $startDate = $offerFamily['OfferFamily']['startDate'];
        $newEndDate = $this->data['OfferFamily']['endDate'];
    
        if (strtotime($startDate) >= strtotime($newEndDate) || ($offerFamily['OfferFamily']['endDate'] != $newEndDate && time() >= strtotime($newEndDate))) return false;

        return true;
    }
    
    function checkIfOfferIsLive($data) {
	    $offerFamily = $this->find('first', array('conditions' => array('OfferFamily.offerId' => $this->data['OfferFamily']['offerId'])));
    
        if ($offerFamily['OfferFamily']['isClosed']) return false;

        return true;
    }
    
    function afterSave($created) {
        //TODO: update instance with new date
        if (!$created && !empty($this->data['OfferFamily']['endDate'])) {
            $results = $this->query("SELECT schedulingInstanceId, endDate FROM schedulingInstance as SchedulingInstance INNER JOIN offer USING(schedulingInstanceId) WHERE offerId = ".$this->data['OfferFamily']['offerId']);
            $results = $results[0];
            if($results['SchedulingInstance']['endDate'] != $this->data['OfferFamily']['endDate']) {
                $this->query("UPDATE schedulingInstance SET endDate = '{$this->data['OfferFamily']['endDate']}' WHERE schedulingInstanceId = ".$results['SchedulingInstance']['schedulingInstanceId']); 
            }
        }
        return true;
    }
}
?>
