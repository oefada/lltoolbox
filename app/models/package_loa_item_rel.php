<?php
class PackageLoaItemRel extends AppModel {

	var $name = 'PackageLoaItemRel';
	var $useTable = 'packageLoaItemRel';
	var $primaryKey = 'packageLoaItemRelId';
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'),
						   'Package' => array('foreignKey' => 'packageId')
					);
	var $orderBy = array('LoaItemType.loaItemTypeName');
		
	var $validate = array('quantity' => array('rule' => 'numeric', 'message' => 'Must be a number'));
    
    var $actsAs = array('Logable');
	
    function updateInclusions($package, $isMultiClientPackage) {
        //update only Food and Beverage loa items
        $query = "SELECT * FROM packageLoaItemRel PackageLoaItemRel
                  INNER JOIN loaItem LoaItem ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId
                  INNER JOIN loa Loa ON Loa.loaId = LoaItem.loaId
                  WHERE PackageLoaItemRel.packageId = {$package['Package']['packageId']} AND LoaItem.loaItemTypeId = 5 AND quantity > 1";
        if ($inclusions = $this->query($query)) {
            foreach ($inclusions as $inclusion) {
                if ($isMultiClientPackage) {
                   $quantity = $this->getPackageClientQuantity($loa['Loa']['clientId'], $package['Package']['packageId']);
                }
                else {
                    $quantity = $package['Package']['numNights'];
                }
                $inclusion['PackageLoaItemRel']['quantity'] = $quantity;
                $this->create();
                $this->save($inclusion);
            }
        }
    }
    
    function deletePackageLoaItemRel($packageLoaItemRelId) {
        return $this->delete($packageLoaItemRelId);
    }
    
    function getPackageClientQuantity($clientId, $packageId) {
        $query = "SELECT numNights from loaItemRatePackageRel LoaItemRatePackageRel
                    INNER JOIN loaItemRate LoaItemRate USING (loaItemRateId)
                    INNER JOIN loaItemRatePeriod USING (loaItemRatePeriodId)
                    INNER JOIN loaItem LoaItem USING (loaItemId)
                    INNER JOIN loa Loa USING (loaId)
                    WHERE LoaItem.loaItemTypeId = 22 AND LoaItemRatePackageRel.packageId = {$packageId} AND Loa.clientId = {$clientId};";
        if ($numNights = $this->query($query)) {
            return $numNights[0]['LoaItemRatePackageRel']['numNights'];
        }
    }


}
?>
