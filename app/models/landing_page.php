<?php
class LandingPage extends AppModel {

	var $name = 'LandingPage';
	var $useTable = 'landingPage';
	var $primaryKey = 'landingPageId';
	var $displayField = 'landingPageName';
	var $order = array('LandingPage.landingPageName');
	var $belongsTo = array('LandingPageType' => array('foreignKey' => 'landingPageTypeId'));

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
	
	function afterSave() {
	  if(!empty($this->data['LandingPage']['siteId'])) {
			$lp = $this->findByLandingPageId($this->data['LandingPage']['landingPageId']);
			$siteId = $this->data['LandingPage']['siteId'];
			$this->useDbConfig = AppModel::getDbName($siteId);
			unset($lp['LandingPage']['siteId']);
			$this->saveAll($lp, array('callbacks' => false));
	  }
	}
	
}
?>
