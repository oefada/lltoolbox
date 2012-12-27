<?php
class EventRegistry extends AppModel {

	public $name = 'EventRegistry';
	public $useTable = 'eventRegistry';
	public $primaryKey = 'eventRegistryId';

   var $belongsTo = array(
   						'User' => array('foreignKey' => 'userId'),
   						'EventRegistryType' => array('foreignKey' => 'eventRegistryTypeID'),
					   );

}