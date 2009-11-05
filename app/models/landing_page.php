<?php
class LandingPage extends AppModel {

	var $name = 'LandingPage';
	var $useTable = 'landingPage';
	var $primaryKey = 'landingPageId';
	var $displayField = 'landingPageName';
	var $order = array('LandingPage.landingPageName');
	var $belongsTo = array('LandingPageType' => array('foreignKey' => 'landingPageTypeId'));

	var $hasAndBelongsToMany = array(
						'Menu' => array(
										'with' => 'menuLandingPageRel',
										'foreignKey' => 'landingPageId',
										'associationForeignKey' => 'menuId'
										)
								);
	
	
	var $actsAs = array('Containable', 'Multisite');
}
?>
