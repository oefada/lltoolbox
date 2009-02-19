<?php
class MenuTitleImage extends AppModel {

	var $name = 'MenuTitleImage';
	var $useTable = 'menuTitleImage';
	var $primaryKey = 'menuTitleImageId';
	var $displayField = 'menuTitleImageName';
	
	var $validate = array(
						'headerImageUrl' => array(
											'rule' => array('extension', array('gif', 'jpeg', 'jpg', 'png')),
											'message' => 'Must be a valid image URL ending in .gif, .jpeg, .jpg, or .png'
											)
						);

}
?>