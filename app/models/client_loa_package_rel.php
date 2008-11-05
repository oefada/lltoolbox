<?php
class ClientLoaPackageRel extends AppModel {

	var $name = 'ClientLoaPackageRel';
	var $useTable = 'clientLoaPackageRel';
	var $primaryKey = 'clientLoaPackageRelId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'),
						   'Loa' => array('foreignKey' => 'loaId'),
						   'Client' => array('foreignKey' => 'clientId')
						   );
}
?>
