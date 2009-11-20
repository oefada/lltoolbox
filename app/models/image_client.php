<?php
class ImageClient extends AppModel {
			
			var $name = 'ImageClient';
			var $useTable = 'imageClient';
			var $primaryKey = 'clientImageId';
			
			var $actsAs = array('Containable');
			
			var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'),
																										'Image' => array('className' => 'Image', 'foreignKey' => 'imageId'),
																										'ImageType' => array('className' => 'ImageType', 'foreignKey' => 'imageTypeId')
																										);
}
?>