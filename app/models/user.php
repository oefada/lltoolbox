<?php
class User extends AppModel {

	var $name = 'User';
	var $useTable = 'user';
	var $primaryKey = 'userId';
	
	var $validate = array(	
				'email' => array(
					'rule' => 'email',
					'message' => 'Invalid email address.'
				)
			);

	var $belongsTo = array('Salutation' => array('foreignKey' => 'salutationId'));

	var $hasOne = array('UserSiteExtended' => array('foreignKey' => 'userId'));

	var $hasMany = array('UserMailOptin' => array('foreignKey' => 'userId'),
						 'UserPaymentSetting' => array('foreignKey' => 'userId'),
						 'UserPreference' => array('foreignKey' => 'userId'),
						 'Bid' => array('foreignKey' => 'userId'),
						 'Address' => array('foreignKey' => 'userId'),
						 'UserAcquisitionSource' => array('foreignKey' => 'userAcquisitionSourceId'),
						 'Ticket' => array('foreignKey' => 'userId')
						);

	var $hasAndBelongsToMany = array(
								'Contest' => 
									array('className' => 'Contest',
										  'joinTable' => 'contestUserRel',
										  'foreignKey' => 'userId',
										  'associationForeignKey' => 'contestId'
									)
								);
}
?>
