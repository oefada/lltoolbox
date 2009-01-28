<?php
class Client extends AppModel {

	var $name = 'Client';
	var $useTable = 'client';
	var $primaryKey = 'clientId';
	var $displayField = 'name';
	var $order = array('Client.name');
	var $actsAs = array('Containable');
	
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
	
	var $belongsTo = array(
						   'ClientType' => array('foreignKey' => 'clientTypeId'),
						   'Region' => array('foreignKey' => 'regionId'),
						   'ParentClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId')
					 );
					 
	var $hasMany = array('Loa' => array('foreignKey' => 'clientId'),
						 'Accolade' => array('foreignKey' => 'clientId'),
						 'Audit' => array('foreignKey' => 'foreignId', 'conditions' => array('Audit.class' => 'Client'), 'limit' => 5, 'order' => 'Audit.created DESC'),
						 'ChildClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId'),
						 'ClientContact' => array('className' => 'ClientContact', 'foreignKey' => 'clientId')
						);
	
    var $hasAndBelongsToMany = array(
								'Tag' =>
	                               array('className'    => 'Tag',
	                                     'joinTable'    => 'clientTag',
	                                     'foreignKey'   => 'clientId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'unique'       => true,
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

    function afterFind($results, $primary = false) {
        if ($primary == true):
		foreach ($results as $key => $val):
			if (!empty($val['Client']) && is_int($key)):
			    //TODO: Turn the following two queries into one
			    $loas = $this->Loa->find('list', array('contain' => array(), 'fields' => array('loaId'), 'conditions' => array('Loa.clientId' => $val['Client']['clientId'])));
			    $currentLoa = $this->Loa->find('first', array('contain' => array('LoaLevel'), 'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'), 'conditions' => array('Loa.clientId' => $val['Client']['clientId'])));

			    $results[$key]['Client']['currentLoaId'] = $currentLoa['Loa']['loaId']; 
			    $results[$key]['ClientLevel']['clientLevelId'] = $currentLoa['LoaLevel']['loaLevelId'];
			    $results[$key]['ClientLevel']['clientLevelName'] = $currentLoa['LoaLevel']['loaLevelName'];
				$results[$key]['Client']['numLoas'] = count($loas);				
			endif;
		endforeach;
	    endif;
	return $results;
	}

	// this is for integration of clients to live clients. 
	function afterSave($created) {
		if ($created) {
			$tmp = "CLIENT CREATED\n\n";	
		} else {
			$tmp = "CLIENT UPDATED\n\n";	
		}
		$tmp.= print_r($this->data, true);
		mail('alee@luxurylink.com', 'client integration testing', $tmp);
		die();
	}

}
?>
