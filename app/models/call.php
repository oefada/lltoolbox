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

	public static $interactionTypes = array(
		1 => 'Guest',
		2 => 'Client',
	);
	public static $contactTypes = array(
		1 => 'Inbound Call',
		2 => 'Outbound Call',
		3 => 'Live Chat Reactive',
		4 => 'Live Chat Proactive',
	);
	public static $contactTopics = array(
		1 => 'Haven\'t received confirmation - checking confirmation',
		2 => 'Checking availability before purchase',
		/* 3 => 'Checking availability before purchase - Unmet demand', DELETED */
		4 => 'Checking availability before purchase - PHG',
		5 => 'General "how to" website questions',
		/* 6 => 'Non "how to" travel question - Help me find a package', DELETED */
		7 => 'Registration/Login Issues',
		8 => 'Glitches/Bugs',
		9 => 'Change of dates request',
		10 => 'Modify a package',
		11 => 'Cancellation/Refund',
		12 => 'Promos/Credits',
		13 => 'Vendor Call (someone selling something)',
		14 => 'Post trip feedback about the property or services',
		/* 15 => 'Hotel Confirmation Call, or Follow Up', DELETED */
		16 => 'Vcom Questions',
		/* 17 => 'Unmet demand - location', DELETE */
		18 => 'Unmet demand - property selection / type',
		19 => 'Specific questions on a package',
		20 => 'Customer shopping',
		21 => 'Immediate issue at hotel',
		25 => 'Buy Now Follow-up',
		999 => 'Other',
	);

}
