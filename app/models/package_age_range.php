<?php
class PackageAgeRange extends AppModel {

	var $name = 'PackageAgeRange';
	var $useTable = 'packageAgeRange';
	var $primaryKey = 'packageAgeRangeId';
	var $order = 'rangeLow ASC';
}
?>