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
    
    function updateFromPackage($data, $packageId, $loaItemRatePeriodId) {
        $query = "SELECT loaItemId FROM loaItemRatePeriod LoaItemRatePeriod
                  WHERE LoaItemRatePeriod.loaItemRatePeriodId = {$loaItemRatePeriodId}";
        if ($loaItem = $this->query($query)) {
            $loaItemId = $loaItem[0]['LoaItemRatePeriod']['loaItemId'];
        }
        else {
            return false;
        }
        foreach ($data as $fee) {
            if (!empty($fee['feePercent'])) {
                $fee['loaItemId'] = $loaItemId;
                $this->create();
                if ($this->save($fee)) {
                    continue;
                }
                else {
                    return false;
                }
            }
            elseif (empty($fee['feePercent']) && empty($fee['feeName'])) {
                $this->delete($fee['feeId']);
            }
        }
        return true;
    }
}
?>