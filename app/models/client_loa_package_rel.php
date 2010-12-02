<?php
class ClientLoaPackageRel extends AppModel {

	var $name = 'ClientLoaPackageRel';
	var $useTable = 'clientLoaPackageRel';
	var $primaryKey = 'clientLoaPackageRelId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'),
						   'Loa' => array('foreignKey' => 'loaId'),
						   'Client' => array('foreignKey' => 'clientId')
						   );
	var $actsAs = array('Logable');
    
    function getLoaId($packageId) {
        return $this->field('loaId', array('packageId' => $packageId));
    }
    
    function isMultiClientPackage($packageId) {
        $query = "SELECT COUNT(*) AS clientCount
                  FROM clientLoaPackageRel ClientLoaPackageRel
                  WHERE ClientLoaPackageRel.packageId = {$packageId}";
        $count = $this->query($query);
        if ($count[0][0]['clientCount'] > 1) {
            return true;
        }
    }
}
?>
