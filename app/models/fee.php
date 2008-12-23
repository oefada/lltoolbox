<?php
class Fee extends AppModel {

	var $name = 'Fee';
	var $useTable = 'fee';
	var $primaryKey = 'feeId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId')
					);
}
?>