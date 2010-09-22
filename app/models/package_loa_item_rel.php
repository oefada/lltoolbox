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
	
	/*			
	var $hasAndBelongsToMany = array(
								'packageRatePeriod' => 
									array('className' => 'packageRatePeriod',
										  'joinTable' => 'packageRatePeriodItemRel',
										  'foreignKey' => 'packageLoaItemRelId',
										  'associationForeignKey' => 'packageRatePeriodId'
									)
								);*/
    
    function updateInclusions($package) {
        //update only Food and Beverage loa items
        $query = "SELECT * FROM packageLoaItemRel PackageLoaItemRel
                  INNER JOIN loaItem LoaItem ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId
                  WHERE PackageLoaItemRel.packageId = {$package['Package']['packageId']} AND LoaItem.loaItemTypeId = 5 AND quantity > 1";
        if ($inclusions = $this->query($query)) {
            foreach ($inclusions as $inclusion) {
                $inclusion['PackageLoaItemRel']['quantity'] = $package['Package']['numNights'];
                $this->create();
                $this->save($inclusion);
            }
        }
    }
    
    function deletePackageLoaItemRel($packageLoaItemRelId) {
        return $this->delete($packageLoaItemRelId);
    }

}
?>
