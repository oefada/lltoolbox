<?php
class LoaItemRatePackageRel extends AppModel {

    var $name = 'LoaItemRatePackageRel';
    var $useTable = 'loaItemRatePackageRel';
    var $primaryKey = 'loaItemRatePackageRelId';
    
    var $belongsTo = array('Package' => array('foreignKey' => 'packageId'),
                           'LoaItemRate' => array('foreignKey' => 'loaItemRateId')
						);
    
    var $actsAs = array('Logable');
    
    var $dailyRatesMap = array('w0' => 'Su',
                               'w1' => 'M',
                               'w2' => 'T',
                               'w3' => 'W',
                               'w4' => 'Th',
                               'w5' => 'F',
                               'w6' => 'S');
    
    function setNumNights(&$formData) {
        $data = array();
        foreach ($formData['LoaItemRatePackageRel'] as $i => $rate) {
            $fields = array();
            $weekdays = explode('/', $rate['weekDays']);
            foreach ($weekdays as $day) {
                $field = array_search($day, $this->dailyRatesMap);
                $fields[] = "{$field} = 1";
            }
            $dayConditions = implode(' AND ', $fields);
            $query = "SELECT loaItemRatePackageRelId FROM loaItemRatePackageRel LoaItemRatePackageRel
                      INNER JOIN loaItemRate LoaItemRate USING (loaItemRateId)
                      WHERE packageId = {$formData['Package']['packageId']} AND {$dayConditions}";
            $rels = $this->query($query);
            if (!empty($rels)) {
                foreach($rels as $j => $relId) {
                    $data['LoaItemRatePackageRel'][$j]['loaItemRatePackageRelId'] = $relId['LoaItemRatePackageRel']['loaItemRatePackageRelId'];
                    $data['LoaItemRatePackageRel'][$j]['numNights'] = $rate['numNights'];
                }
            }
            unset($formData['LoaItemRatePackageRel'][0]);
            $formData['LoaItemRatePackageRel'] = array_merge($formData['LoaItemRatePackageRel'], $data['LoaItemRatePackageRel']);
        }
    }
    
    function deleteRatesFromPackage($packageId, $loaItemId) {
        $query = "SELECT loaItemRatePackageRelId FROM loaItemRatePackageRel LoaItemRatePackageRel
                  INNER JOIN loaItemRate LoaItemRate USING (loaItemRateId)
                  INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING (loaItemRatePeriodId)
                  INNER JOIN loaItem LoaItem USING (loaItemId)
                  WHERE LoaItemRatePackageRel.packageId = {$packageId} AND LoaItem.loaItemId = {$loaItemId}";
        if ($rates = $this->query($query)) {
            foreach ($rates as $rate) {
                $this->delete($rate['LoaItemRatePackageRel']['loaItemRatePackageRelId']);
            }
        }
        
    }
    
    function getRateDays($packageId) {
        $query = "SELECT * FROM loaItemRate LoaItemRate
              INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING (loaItemRateId)
              WHERE packageId = {$packageId}
              GROUP BY numNights";
        $rates = $this->query($query);
        $rateData = array();
        if (!empty($rates)) {
            foreach ($rates as $i => $rate) {
                $labelArr = array();
                foreach($this->dailyRatesMap as $field => $label) {
                    if ($rate['LoaItemRate'][$field] == 1) {
                        $labelArr[] = $label;
                    }
                }
                $rateData[$i]['rateLabel'] = implode('/', $labelArr);
                $rateData[$i]['numNights'] = $rate['LoaItemRatePackageRel']['numNights'];
            }
        return $rateData;
        }
    }

}
?>