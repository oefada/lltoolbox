<?php
class LandingPage extends AppModel {

	var $name = 'LandingPage';
	var $useTable = 'landingPage';
	var $primaryKey = 'landingPageId';
	var $displayField = 'landingPageName';
	var $order = array('LandingPage.landingPageName');
	var $belongsTo = array('LandingPageType' => array('foreignKey' => 'landingPageTypeId'));

    var $multisite = true;

	var $actsAs = array('Containable');
	
	function beforeSave() {
	  $lp = $this->findByLandingPageId($this->id);
	  if (!empty($lp)) {
			if ($lp['LandingPage']['siteId'] != $this->data['LandingPage']['siteId']) {
				  $this->useDbConfig = AppModel::getDbName($lp['LandingPage']['siteId']);
				  $this->query("DELETE FROM landingPage WHERE landingPageId = {$lp['LandingPage']['landingPageId']}");
				  $this->useDbConfig = 'default';
			}
	  }
	  return true;
	}
	
	function afterSave($created) {
			$this->recursive = -1;
			AppModel::afterSave($created);
	}
	
	function getTravelIdeaSelectList() {
	  $pages = $this->find('all', array('fields' => array('landingPageId', 'landingPageName', 'siteId')));
	  $select = array();
	  foreach ($pages as $page) {
			$site = AppModel::getDbName($page['LandingPage']['siteId']);
			$select[$page['LandingPage']['landingPageId']] = $page['LandingPage']['landingPageName'].' - '.$site;
	  }
	  return $select;
	}
	
}
?>
