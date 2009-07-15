<?php
class Contest extends AppModel {

	var $name = 'Contest';
	var $useTable = 'contest';
	var $primaryKey = 'contestId';
	var $displayField = 'contestName';

	var $hasMany = array('ContestClientRel' => array('foreignKey' => 'contestId'));

}
?>
