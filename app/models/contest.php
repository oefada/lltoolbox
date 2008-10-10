<?php
class Contest extends AppModel {

	var $name = 'Contest';
	var $useTable = 'contest';
	var $primaryKey = 'contestId';
	var $displayField = 'contestName';
	
	var $hasAndBelongsToMany = array(
								'User' => 
									array('className' => 'User',
										  'joinTable' => 'contestUserRel',
										  'foreignKey' => 'contestId',
										  'associationForeignKey' => 'userId'
									)
								);

}
?>
