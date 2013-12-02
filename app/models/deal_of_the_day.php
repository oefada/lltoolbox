<?php
class DealOfTheDay extends AppModel {

	var $name = 'DealOfTheDay';
	var $useTable = 'dealOfTheDay';
	var $primaryKey = 'dealOfTheDayId';
	var $displayField = 'dealOfTheDayId';
	var $order = array('DealOfTheDay.dateActive');

    // var $multisite = true;

	// var $actsAs = array('Containable');






}
?>
