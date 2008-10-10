<?php
class User extends AppModel {

	var $name = 'User';
	var $useTable = 'user';
	var $primaryKey = 'userId';

	var $belongsTo = array('Salutation' => array('foreignKey' => 'salutationId'));
	
	var $hasMany = array('UserMailOptin' => array('foreignKey' => 'userId'),
						 'UserReferral' => array('foreignKey' => 'userId'),
						 'UserSiteExtended' => array('foreignKey' => 'userId'),
						 'UserPaymentSetting' => array('foreignKey' => 'userId'),
						 'UserPreference' => array('foreignKey' => 'userId'),
						 'Bid' => array('foreignKey' => 'userId'),
						 'Address' => array('foreignKey' => 'userId'),
						 'UserAcquisitionSource' => array('foreignKey' => 'userAcquisitionSourceId')
						);

	var $hasAndBelongsToMany = array(
								'Contest' => 
									array('className' => 'Contest',
										  'joinTable' => 'contestUserRel',
										  'foreignKey' => 'userId',
										  'associationForeignKey' => 'contestId'
									),
								'Client' => 
									array('className' => 'Client',
										  'joinTable' => 'clientUserRel',
										  'foreignKey' => 'userId',
										  'associationForeignKey' => 'clientId'
									)
								);
}
?>
