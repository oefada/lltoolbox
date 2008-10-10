<?php
class Currency extends AppModel {

	var $name = 'Currency';
	var $useTable = 'currency';
	var $primaryKey = 'currencyId';
	var $displayField = 'currencyName';
}
?>