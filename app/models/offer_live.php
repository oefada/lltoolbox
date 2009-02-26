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
    
    function beforeSave($created) {
        //TODO: update instance with new date
        return true;
    }
}
?>