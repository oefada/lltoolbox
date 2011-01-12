<?php
class PricePointRatePeriodRel extends AppModel {

    var $name = 'PricePointRatePeriodRel';
    var $useTable = 'pricePointRatePeriodRel';
    var $primaryKey = 'pricePointRatePeriodRelId';
    
    var $belongsTo = array(
        'LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'),
        'PricePoint' => array('foreignKey' => 'pricePointId')
    );
    
    function getPricePointForRatePeriod($packageId, $loaItemRatePeriodId) {
        $query = "SELECT * FROM pricePoint PricePoint
                  INNER JOIN pricePointRatePeriodRel PricePointRatePeriodRel USING(pricePointId)
                  INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
                  WHERE PricePoint.packageId = {$packageId} AND PricePointRatePeriodRel.loaItemRatePeriodId = {$loaItemRatePeriodId}";
        if ($pricePoint = $this->query($query)) {
            return $pricePoint[0];
        }
        else {
            return false;
        }
    }
}
?>