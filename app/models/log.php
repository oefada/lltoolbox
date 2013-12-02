<?php
class Log extends AppModel {

	var $name = 'Log';
	var $useTable = 'logs';
	var $primaryKey = 'id';
	var $order = "created DESC";
}
?>
