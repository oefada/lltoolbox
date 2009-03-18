<?php
class HomepageMerchandising extends AppModel {

	var $name = 'HomepageMerchandising';
	var $belongsTo = array('HomepageMerchandisingType' => array('foreignKey' => 'homepageMerchandisingTypeId'));

}
?>
