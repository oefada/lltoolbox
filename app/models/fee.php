<?php
class Fee extends AppModel {

	var $name = 'Fee';
	var $useTable = 'fee';
	var $primaryKey = 'feeId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId')
					);
    
    /**
     * Package revamp functions
     **/
        
    function getFeesForRoomType($roomTypeId) {
        $query = "SELECT feeId, feeName, feePercent, feeTypeId FROM fee Fee
                  WHERE loaItemId = {$roomTypeId}
                  ORDER BY feeTypeId";
        return $this->query($query);
    }
    
    //acarney 2011-01-06 -- replace getFeesForRoomType with this function
    function getFees($loaItemId) {
        $query = "SELECT feeId, feeName, feePercent, feeTypeId FROM fee Fee
                  WHERE loaItemId = {$loaItemId}
                  ORDER BY feeTypeId";
        return $this->query($query);
    }
    
    function addFees($loaItemId, $price) {
        $taxes = 0;
        if ($fees = $this->getFees($loaItemId)) {
            foreach ($fees as $fee) {
                if ($fee['Fee']['feeTypeId'] == 1) {            //percentage
                    $taxes += ($fee['Fee']['feePercent']/100)*$price;
                }
                elseif ($fee['Fee']['feeTypeId'] == 2) {       //dollar amount
                    $taxes += $fee['Fee']['feePercent'];
                }
            }
        }
        return $price + $taxes;
    }
    
    function updateFromPackage($data, $loaItems, $packageId) {
        foreach($loaItems as $item) {
            foreach ($data as $fee) {
                if (!empty($fee['feePercent'])) {
                    $fee['loaItemId'] = $item['LoaItem']['loaItemId'];
                    $this->create();
                    $saved = $this->save($fee);
                    if (!$saved) {
                        return false;
                    }
                }
                elseif (empty($fee['feePercent']) && empty($fee['feeName'])) {
                    $this->delete($fee['feeId']);
                }
            }
        }
        return true;
    }
}
?>