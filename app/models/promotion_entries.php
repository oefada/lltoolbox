<?php
class PromotionEntries extends AppModel {
	
	var $name = 'PromotionEntries';
	var $useTable = 'promotionEntries';
	var $primaryKey = 'id';
	var $belongsTo = Array('Promotions' => Array('foreignKey' => 'promotionId'));

}
?>
