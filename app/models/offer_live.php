<?php
class OfferLive extends AppModel {

	var $name = 'OfferLive';
	var $useTable = 'offerLive';
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
	    $offerLive = $this->find('first', array('conditions' => array('OfferLive.offerId' => $this->data['OfferLive']['offerId'])));

        $startDate = $offerLive['OfferLive']['startDate'];
        $newEndDate = $this->data['OfferLive']['endDate'];
    
        if (strtotime($startDate) >= strtotime($newEndDate) || ($offerLive['OfferLive']['endDate'] != $newEndDate && time() >= strtotime($newEndDate))) return false;

        return true;
    }
    
    function checkIfOfferIsLive($data) {
	    $offerLive = $this->find('first', array('conditions' => array('OfferLive.offerId' => $this->data['OfferLive']['offerId'])));
    
        if ($offerLive['OfferLive']['isClosed']) return false;

        return true;
    }
    
    function afterSave($created) {
        //TODO: update instance with new date
        if (!$created && !empty($this->data['OfferLive']['endDate'])) {
            $results = $this->query("SELECT schedulingInstanceId, endDate FROM schedulingInstance as SchedulingInstance INNER JOIN offer USING(schedulingInstanceId) WHERE offerId = ".$this->data['OfferLive']['offerId']);
            $results = $results[0];
            if($results['SchedulingInstance']['endDate'] != $this->data['OfferLive']['endDate']) {
                $this->query("UPDATE schedulingInstance SET endDate = '{$this->data['OfferLive']['endDate']}' WHERE schedulingInstanceId = ".$results['SchedulingInstance']['schedulingInstanceId']); 
            }
        }
        return true;
    }
}
?>