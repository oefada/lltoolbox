<?php
class PricePoint extends AppModel {

	var $name = 'PricePoint';
	var $useTable = 'pricePoint';
	var $primaryKey = 'pricePointId';
	var $displayField = 'name';
    
    var $belongsTo = array(
        'Package' => array('foreignKey' => 'packageId')
    );

    var $hasMany = array(
        'PricePointRatePeriodRel' => array('foreignKey' => 'pricePointId'),
        'SchedulingMaster' => array('foreignKey' => 'pricePointId')
    );
    
    var $actsAs = array('Logable');

		function setInactive($val,$ppid){

			$q="UPDATE pricePoint SET inactive=$val WHERE pricePointId=$ppid";
			$this->query($q);

		}
    
    function afterSave($created) {
        $pricePointId = (isset($this->data['PricePoint']['pricePointId'])) ? isset($this->data['PricePoint']['pricePointId']) : $this->id;
        $packageId = $this->data['PricePoint']['packageId'];
        
        // get the site and offer table for the package first
        $rows = $this->query("SELECT siteId FROM package Package WHERE packageId = $packageId");
        switch ($rows[0]['Package']['siteId']) {
            case '1':
            $offerTable = 'offerLuxuryLink';
            break;
            
            case '2':
            $offerTable = 'offerFamily';
            break;
            
            default:
            $offerTable = false;
            break;
        }
        
        // update buyNow offers with the latest pricing
        if ($offerTable) {
            $buyNowPrice = round($this->data['PricePoint']['retailValue'] * $this->data['PricePoint']['percentRetailBuyNow'] / 100); 
            $this->query("
                UPDATE $offerTable
                SET openingBid = $buyNowPrice, buyNowPrice = $buyNowPrice
                WHERE pricePointId = $pricePointId AND packageId = $packageId AND offerTypeId IN(3, 4)
            ");
        }
    }

    function getPricePoint($packageId) {
        return $this->query("
            SELECT *, GROUP_CONCAT(CONCAT(DATE_FORMAT(startDate, '%b %e, %Y'), ' - ', DATE_FORMAT(endDate, '%b %e, %Y')) SEPARATOR '<br/>') dateRanges
            FROM pricePoint PricePoint
            INNER JOIN pricePointRatePeriodRel PricePointRatePeriodRel USING(pricePointId)
            INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
            INNER JOIN loaItemDate LoaItemDate USING(loaItemRatePeriodId)
            WHERE packageId = $packageId
            GROUP BY pricePointId
        ");
    }
    
    function getPricePointValidities($packageId) {
        return $this->query("
            SELECT pricePointId, startDate, endDate
            FROM pricePoint PricePoint
            INNER JOIN pricePointRatePeriodRel PricePointRatePeriodRel USING(pricePointId)
            INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
            INNER JOIN loaItemDate LoaItemDate USING(loaItemRatePeriodId)
            WHERE packageId = $packageId
            ORDER BY endDate
        ");
    }
    
    function getPricePointStartEnd($pricePointId) {
        $rows = $this->query("
            SELECT pricePointId, min(startDate) AS startDate, max(endDate) AS endDate
            FROM pricePoint PricePoint
            INNER JOIN pricePointRatePeriodRel PricePointRatePeriodRel USING(pricePointId)
            INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
            INNER JOIN loaItemDate LoaItemDate USING(loaItemRatePeriodId)
            WHERE pricePointId = $pricePointId
            ORDER BY endDate
        ");
        return $rows[0][0];
    }
    
    function getLoaItemRatePeriod($packageId) {
        return $this->query("
            SELECT *
            FROM pricePoint PricePoint
            INNER JOIN pricePointRatePeriodRel PricePointRatePeriodRel USING(pricePointId)
            INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
            WHERE packageId = $packageId
        ");
    }
    
    function createHotelOfferPricePoint($packageId) {
        $packageQuery = "SELECT Package.validityStartDate, Package.validityEndDate
                         FROM package Package
                         WHERE Package.packageId = {$packageId}";
        if ($packageValidity = $this->query($packageQuery)) {
            $startDate = $packageValidity[0]['Package']['validityStartDate'];
            $endDate = $packageValidity[0]['Package']['validityEndDate'];
            $ppQuery = "SELECT pricePointId
                        FROM pricePoint
                        WHERE packageId = {$packageId} AND validityStart = '{$startDate}' AND validityEnd = '{$endDate}'";
            if ($ppExists = $this->query($ppQuery)) {
                return;
            }
            else {
                $pricePoint = array('packageId' => $packageId,
                                    'name' => 'Hotel Offer',
                                    'validityStart' => $startDate,
                                    'validityEnd' => $endDate);
                if ($this->save($pricePoint)) {
                    return $this->getLastInsertID();
                }
            }
        }
    }
    
    function getHotelOfferPricePoint($packageId) {
        return ($this->query("SELECT * FROM pricePoint PricePoint
                             WHERE PricePoint.packageId = {$packageId}
                             ORDER BY PricePoint.pricePointId DESC
                             LIMIT 1"));
    }
}
?>
