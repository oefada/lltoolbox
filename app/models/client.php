<?php
class Client extends AppModel {

	var $name = 'Client';
	var $useTable = 'client';
	var $primaryKey = 'clientId';
	var $displayField = 'name';
	var $order = array('Client.name');
	var $actsAs = array('Diffable');
	
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
						   'ClientAcquisitionSource' => array('foreignKey' => 'clientAcquisitionSourceId',
						   'ParentClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId'))
					 );
					 
	var $hasMany = array('Loa' => array('foreignKey' => 'clientId'),
						 'Accolade' => array('foreignKey' => 'clientId'),
						 'Audit' => array('foreignKey' => 'foreignId', 'conditions' => array('Audit.class' => 'Client'), 'limit' => 5, 'order' => 'Audit.created DESC'),
						 'ChildClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId')
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
								   ),
								'Theme' => 
									array('className' => 'Theme',
										  'joinTable' => 'clientThemeRel',
										  'foreignKey' => 'clientId',
										  'associationForeignKey' => 'themeId'
								   ),
								'Destination' => 
									array('className' => 'Destination',
										  'joinTable' => 'clientDestinationRel',
										  'foreignKey' => 'clientId',
										  'associationForeignKey' => 'destinationId'
								   )
                               );
	function afterFind($results) {
		foreach ($results as $key => $val):
			if (isset($val['Client']) && is_int($key)):
			    $this->Loa->recursive = -1;
				$count = $this->Loa->find('first', array('fields' => array('COUNT(*) AS numLoas'), 'conditions' => array('clientId' => $val['Client']['clientId'])));
				$results[$key]['Client']['numLoas'] = $count[0]['numLoas'];	
			endif;
		endforeach;
		
	return $results;
	}
}
?>
