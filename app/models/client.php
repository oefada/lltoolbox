<?php
class Client extends AppModel {

	var $name = 'Client';
	var $useTable = 'client';
	var $primaryKey = 'clientId';
	var $displayField = 'name';
	var $order = array('Client.name');
	
	var $validate = array(
				'name' => array(
					'rule' => '/[a-zA-Z0-9]/',
					'message' => 'Client name must only contain letters.'
				),
				'email' => array(
					'rule' => 'email',
					'message' => 'Invalid email address.'
				)
			);
	
	var $belongsTo = array('ClientLevel' => array('foreignKey' => 'clientLevelId'),
						   'ClientStatus' => array('foreignKey' => 'clientStatusId'),
						   'ClientType' => array('foreignKey' => 'clientTypeId'),
						   'Region' => array('foreignKey' => 'regionId'),
						   'ClientAcquisitionSource' => array('foreignKey' => 'clientAcquisitionSourceId')
					 );
					 
	var $hasMany = array('Loa' => array('foreignKey' => 'clientId'),
					     'Address' => array('foreignKey' => 'clientId'),
						 'ClientThemeRel' => array('foreignKey' => 'clientId'),
						 'Accolade' => array('foreignKey' => 'clientId')
						);
	
    var $hasAndBelongsToMany = array(
								'Tag' =>
	                               array('className'    => 'Tag',
	                                     'joinTable'    => 'clientTag',
	                                     'foreignKey'   => 'clientId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'unique'       => true,
	                               ),
								'User' => 
									array('className' => 'User',
										  'joinTable' => 'clientUserRel',
										  'foreignKey' => 'clientId',
										  'associationForeignKey' => 'userId'
								   ),
								'Amenity' => 
									array('className' => 'Amenity',
										  'joinTable' => 'clientAmenityRel',
										  'foreignKey' => 'clientId',
										  'associationForeignKey' => 'amenityId'
								   )
                               ); 		
}
?>
