<?php
class SandboxController extends AppController
{

	var $name = 'Sandbox';
	var $uses = array(
		'ImageClient',
		'Image',
		'Ticket',
		'ClientLoaPackageRel',
	);

	function index()
	{
		$z = $this->Ticket->field('packageId', array('Ticket.ticketId' => 196561));
		$this->set('sandbox', $z);
	}

}
