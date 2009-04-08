<?php
class ClientScore extends AppModel {

	var $name = 'ClientScore';
	var $useTable = 'clientScore';
	var $primaryKey = 'clientScoreId';
	var $belongsTo = array(
						'ClientScoreType' => array('foreignKey' => 'clientScoreTypeId'),
						'Client' => array('foreignKey' => 'clientId')
					);
}
?>
