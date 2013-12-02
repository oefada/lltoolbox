<?php
class Call extends AppModel
{

	/*
	 * Someday the values here can be moved to a database table, but for now
	 * leaving it in the model for performance reasons.
	 */

	var $name = 'Call';
	var $primaryKey = 'callId';

	var $belongsTo = array(
		'Ticket' => array(
			'className' => 'Ticket',
			'foreignKey' => 'ticketId',
			'fields' => array('ticketId'),
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'userId',
			'fields' => array('userId','firstName','lastName'),
		),
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'clientId',
			'fields' => array('clientId','name'),
		),
	);

	var $validate = array(
		'contactTopic' => 'numeric',
		'ticketId' => array(
			'rule' => 'numeric',
			'message' => 'Enter a valid ticket id',
			'allowEmpty' => true,
		),
		'userId' => array(
			'rule' => 'numeric',
			'message' => 'Enter a valid user id',
			'allowEmpty' => true,
		),
		'clientId' => array(
			'rule' => 'numeric',
			'message' => 'Enter a valid client id',
			'allowEmpty' => true,
		),
	);

	// TODO READ THESE FROM THE DATABASE
	public static $interactionTypes = array(
		1 => 'Guest',
		2 => 'Client',
	);
	// TODO READ THESE FROM THE DATABASE
	public static $contactTypes = array(
		1 => 'Inbound Call',
		2 => 'Outbound Call',
		3 => 'Live Chat Reactive',
		4 => 'Live Chat Proactive',
	);

    /**
     * Moved to Database
    **/

}
