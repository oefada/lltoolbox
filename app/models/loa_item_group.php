<?php
class LoaItemGroup extends AppModel {

	var $name = 'LoaItemGroup';
	var $useTable = 'loaItemGroup';
	var $primaryKey = 'loaItemGroupId';

	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
}
?>
