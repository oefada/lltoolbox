<?php
class Menu extends AppModel {

	var $name = 'Menu';
	var $useDbConfig = 'live';
	var $useTable = 'menu';
	var $primaryKey = 'menuId';
	
	var $hasMany = array('MenuItem' => array('foreignKey' => 'menuId'));
	
	var $belongsTo = array(
						'MenuTitleImage' => array('className' => 'MenuTitleImage', 'foreignKey' => 'menuTitleImageId')
						);
	
	var $hasAndBelongsToMany = array(
						'Style' => array(
										'joinTable' => 'menuStyleRel',
										'foreignKey' => 'menuId',
										'associationForeignKey' => 'referenceId'
										)
								);
								
	var $validate = array(
						'menuName' => array('rule' => VALID_NOT_EMPTY)
						);
	
	var $order = array('Menu.weight', 'Menu.menuName');
	
	function afterSave($created) {
		if($created) {
			$this->data['Menu']['weight'] = $this->__getNewWeight();
			$this->save($this->data);
		}
	}
	
	function __getNewWeight() {		
		$weight = $this->query("SELECT ( MAX(weight) + 1 ) as newWeight FROM menu");
		return $weight[0][0]['newWeight'];
	}
	
	function __sort($a, $b) {
		return $a['weight'] > $b['weight'];
	}						
}
?>