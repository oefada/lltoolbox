<?php
class PackagePromoRel extends AppModel {
	var $name = 'PackagePromoRel';
	var $useTable = 'packagePromoRel';
	var $primaryKey = 'packagePromoRelId';
	
	var $belongsTo = array(
						   'Client' => array('foreignKey' => 'clientId'),
						   'Package' => array('foreignKey' => 'packageId')
					 );
	
	var $validate = array('clientId' => array('rule' => array('validateClient'),
												'message' => 'This ID does not belong to an active client'
												),
							'packageId' => array(
								'rule' => array('validateClientPackage'),
								'message' => 'This is not a valid Package ID'
							)
						);
						
	function validateClientPackage($data) {
		$this->Package->recursive = -1;
		$package = $this->Package->find('first', array('conditions' => $data));
		return !empty($package);
	}
	
	function validateClient($data) {
		$this->Client->recursive = -1;
		$client = $this->Client->find('first', array('conditions' => $data));
		return !empty($client);
	}
}
?>