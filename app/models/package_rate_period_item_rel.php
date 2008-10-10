<?php
class PackageRatePeriodItemRel extends AppModel {

	var $name = 'PackageRatePeriodItemRel';
	var $useTable = 'packageRatePeriodItemRel';
	var $primaryKey = 'packageRatePeriodItemRelId';

	var $belongsTo = array('PackageLoaItemRel' => array('foreignKey' => 'packageLoaItemRelId'),
						   'PackageRatePeriod' => array('foreignKey' => 'packageRatePeriodId')
						   );

}
?>