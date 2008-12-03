<?php
class MenuItem extends AppModel {

	var $name = 'MenuItem';
	var $useDbConfig = 'live';
	var $useTable = 'menuItem';
	var $primaryKey = 'menuItemId';
	
	var $belongsTo = array(
						'Menu' => array('foreignKey' => 'menuId')
						);
						
	var $validate = array(
						'menuItemName' => VALID_NOT_EMPTY,
						'linkTo' => array('rule' => array('validateLinkTo'),
									'message' => 'Must be a complete URL, or a style from the drop down.'),
						'weight' => array('rule' => 'numeric', 
									'allowEmpty' => true,
									'message' => 'Must be a valid negative or positive integer.')
						);
						
	var $order = array('MenuItem.weight', 'MenuItem.menuItemName');
	
	function validateLinkTo($data) {
		if(0 == $this->data['MenuItem']['externalLink']):
			return is_numeric($data['linkTo']);
		else:
			return Validation::url($data['linkTo']);
		endif;
	}
	
	function __getNewWeight($menuId) {
		$weight = $this->query("SELECT ( MAX(weight) + 1 ) as newWeight FROM menuItem WHERE menuId = '$menuId'");
		return $weight[0][0]['newWeight'];
	}
	
	function afterSave($created) {
		if($created) {
			$this->data['MenuItem']['weight'] = $this->__getNewWeight($this->data['MenuItem']['menuId']);
			$this->save($this->data);
		}
	}
}
?>