<?php
class LoaItemRate extends AppModel {

	var $name = 'LoaItemRate';
	var $useTable = 'loaItemRate';
	var $primaryKey = 'loaItemRateId';

	var $belongsTo = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'));
	
	var $hasMany = array('LoaItemRatePackageRel' => array('foreignKey' => 'loaItemRateId'));
	
	function getNumNights($loaItemRatePeriodId) {
		$query = "SELECT w0+w1+w2+w3+w4+w5+w6 AS rateNights
			  FROM loaItemRate LoaItemRate
			  WHERE loaItemRatePeriodId = {$loaItemRatePeriodId}";
		if ($rateNights = $this->query($query)) {
			return $rateNights[0][0]['rateNights'];
		}
		else {
			return 0;
		}
	}
    
    function getRates($loaItemRatePeriodId) {
        $query = "SELECT * FROM loaItemRate LoaItemRate
                  WHERE LoaItemRate.loaItemRatePeriodId = {$loaItemRatePeriodId}";
        return $this->query($query);
    }
    
    function getRoomRates($loaItemId, $packageId, $loaItemRatePeriodId=null) {
        $query = "SELECT LoaItemRate.*, LoaItemRatePackageRel.* FROM loaItemRate LoaItemRate
                  INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING (loaItemRateId)
                  INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING (loaItemRatePeriodId)
                  INNER JOIN loaItem LoaItem USING (loaItemId)
                  WHERE LoaItem.loaItemId = {$loaItemId} AND LoaItemRatePackageRel.packageId = {$packageId}";
        if ($loaItemRatePeriodId) {
            $query .= " AND LoaItemRatePeriod.loaItemRatePeriodId = {$loaItemRatePeriodId}";
        }
        return $this->query($query);
    }
    
    function updateFromPackage($data, $packageId, $loaItemRatePeriodId) {
        foreach ($data as $j => $loaItemRate) {
            $newRate = (isset($loaItemRate['isNew'])) ? true : false;
            $loaItemRatePackageRel = $loaItemRate['LoaItemRatePackageRel'];
            $numNights = $loaItemRate['LoaItemRatePackageRel']['numNights'];
            if ($newRate) {
                for ($i=0; $i<=6; $i++) {
                    if (isset($loaItemRate['w'.$i])) {
                        $loaItemRate['w'.$i] = 1;
                    }
                    else {
                        $loaItemRate['w'.$i] = 0;
                    }
                }
                if (empty($loaItemRate['loaItemRatePeriodId'])) {
                    $loaItemRate['loaItemRatePeriodId'] = $loaItemRatePeriodId;
                }
                $this->create();
                if ($this->save($loaItemRate)) {
                    if ($loaItemRateId = $this->getLastInsertID()) {
                        $rel = array('loaItemRateId' => $loaItemRateId,
                                     'packageId' => $packageId,
                                     'numNights' => $numNights);
                        $this->LoaItemRatePackageRel->create();
                        $this->LoaItemRatePackageRel->save($rel);
                    }
                    else {
                        $this->LoaItemRatePackageRel->create();
                        $this->LoaItemRatePackageRel->save($loaItemRatePackageRel);
                    }
                    continue;
                }
            }
            else {
                $loaItemRatePackageRel['loaItemRateId'] = $loaItemRate['loaItemRateId'];
                $loaItemRatePackageRel['numNights'] = $numNights;
                $this->LoaItemRatePackageRel->create();
                $this->LoaItemRatePackageRel->save($loaItemRatePackageRel);                    
            }
        }
        return true;
    }
    
    function deleteFromPackage($data) {
        foreach ($data as $rate) {
            if (!empty($rate['loaItemRateId'])) {
                if (!empty($rate['LoaItemRatePackageRel'])) {
                    $this->LoaItemRatePackageRel->delete($rate['LoaItemRatePackageRel']['loaItemRatePackageRelId']);
                }
                //$this->delete($rate['loaItemRateId']);
            }
        }
    }
    
    
}
?>
