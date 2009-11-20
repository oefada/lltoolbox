<?php
class Image extends AppModel {

			var $name = 'Image';
			var $useTable = 'image';
			var $primaryKey = 'imageId';
			
			var $actsAs = array('Containable');
			
			var $hasMany = array('ImageClient' => array('className' => 'ImageClient', 'foreignKey' => 'imageId'));

}
?>