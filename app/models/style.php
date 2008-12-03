<?php
class Style extends AppModel {

	var $name = 'Style';
	var $useDbConfig = 'live';
	var $useTable = 'style_mstr';
	var $primaryKey = 'styleId';
	var $displayField = 'styleName';
	var $order = array('Style.styleName');

	var $hasAndBelongsToMany = array(
						'Menu' => array(
										'joinTable' => 'menuStyleRel',
										'foreignKey' => 'referenceId',
										'associationForeignKey' => 'menuId'
										)
								);
	
	
	var $actsAs = array('Containable');
}
?>