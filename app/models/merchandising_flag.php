<?php
class MerchandisingFlag extends AppModel {

	var $name = 'MerchandisingFlag';
	var $useTable = 'merchandisingFlag';
	var $primaryKey = 'merchandisingFlagId';
	var $displayField = 'merchandisingFlagName';
	
	var $order = "merchandisingFlagName";
}
?>