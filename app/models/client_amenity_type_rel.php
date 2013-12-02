<?php
class ClientAmenityTypeRel extends AppModel {

		var $name = 'ClientAmenityTypeRel';
		var $useTable = 'clientAmenityTypeRel';
		var $primaryKey = 'clientAmenityTypeRelId';

        var $multisite = true;
        var $inheritsFrom = array('modelName' => 'Client', 'siteField' => 'sites');
}
?>
