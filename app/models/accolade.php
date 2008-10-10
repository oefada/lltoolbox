<?php
class Accolade extends AppModel {

	var $name = 'Accolade';
	var $useTable = 'accolade';
	var $primaryKey = 'accoladeId';

	var $belongsTo = array('AccoladeSource' => array('foreignKey' => 'accoladeSourceId'));
}
?>
