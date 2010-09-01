<?php
class PricePointRatePeriodRel extends AppModel {

    var $name = 'PricePointRatePeriodRel';
    var $useTable = 'pricePointRatePeriodRel';
    var $primaryKey = 'pricePointRatePeriodRelId';
    
    var $belongsTo = array(
        'LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'),
        'PricePoint' => array('foreignKey' => 'pricePointId')
    );
}
?>