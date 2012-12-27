<?php
class EventRegistryDonor extends AppModel {

	public $name = 'EventRegistryDonor';
	public $useTable = 'eventRegistryDonor';
	public $primaryKey = 'eventRegistryDonorId';


   var $belongsTo = array(
   						'User' => array('foreignKey' => 'userId')
					   );
}