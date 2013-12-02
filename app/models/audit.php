<?php
class Audit extends AppModel
{
	var $name = 'Audit';
	var $actsAs = array('Polymorphic');
	var $order = 'Audit.created DESC';
}
?>