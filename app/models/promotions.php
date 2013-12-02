<?php
class Promotions extends AppModel {
	
	var $name = 'Promotions';
	var $useTable = 'promotions';
	var $primaryKey = 'id';
	var $hasMany = Array('PromotionEntries' => Array('foreignKey' => 'promotionId'));
	
}
?>