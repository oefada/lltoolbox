<?php
class Offer extends AppModel {

	var $name = 'Offer';
	var $useTable = 'offer';
	var $primaryKey = 'offerId';
	
	var $belongsTo = array('SchedulingInstance' => array('foreignKey' => 'schedulingInstanceId'));
	
	//var $hasOne = array('SchedulingInstance' => array('foreignKey' => 'schedulingInstanceId'));

	var $hasMany = array('Bid' => array('foreignKey' => 'offerId'));
    
    function findOfferBySchedulingMasterId($schedulingMasterId) {
        $query = "SELECT offerId FROM offer Offer
                  INNER JOIN schedulingInstance SchedulingInstance USING (schedulingInstanceId)
                  INNER JOIN schedulingMaster SchedulingMaster USING (schedulingMasterId)
                  WHERE SchedulingMaster.schedulingMasterId = {$schedulingMasterId}";
        if ($offer = $this->query($query)) {
            return $offer[0]['Offer']['offerId'];
        }
    }
    
    function getOfferPricePointId($offerId, $siteId) {
        switch ($siteId) {
            case 1:
                $table = 'offerLuxuryLink';
                break;
            case 2:
                $table = 'offerFamily';
                break;
            default:
                return false;
        }
        $query = "SELECT pricePointId FROM {$table}
                  WHERE offerId = {$offerId}";
        if ($pricePoint = $this->query($query)) {
            return $pricePoint[0][$table]['pricePointId'];
        }
    }
    
}
?>
